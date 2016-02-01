<?php

namespace App\Providers;

use Doctrine\Common\Annotations\AnnotationReader;
use Illuminate\Support\ServiceProvider;

use App\Providers\Validator\ConstraintValidatorFactory;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Validator\Mapping\Loader\LoaderChain;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\Validation;

use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\NativeSessionTokenStorage;
//use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;

use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension as FormValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\ResolvedFormTypeFactory;

use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Bridge\Twig\Form\TwigRenderer;

class SymfonyFormServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerValidator();
        $this->registerCsrf();
        $this->registerForm();
        $this->registerTwigFormExtension();

        $this->app->bind(
            'Symfony\Component\Form\FormFactoryInterface',
            $this->app['form.factory']
        );
    }

    protected function registerForm()
    {
        if (!class_exists('Locale') && !class_exists('Symfony\Component\Locale\Stub\StubLocale')) {
            throw new \RuntimeException('You must either install the PHP intl extension or the Symfony Locale Component to use the Form extension.');
        }
        if (!class_exists('Locale')) {
            $r = new \ReflectionClass('Symfony\Component\Locale\Stub\StubLocale');
            $path = dirname(dirname($r->getFilename())).'/Resources/stubs';
            require_once $path.'/functions.php';
            require_once $path.'/Collator.php';
            require_once $path.'/IntlDateFormatter.php';
            require_once $path.'/Locale.php';
            require_once $path.'/NumberFormatter.php';
        }
        $this->app['form.types'] = function ($app) {
            return array();
        };
        $this->app['form.type.extensions'] = function ($app) {
            return array();
        };
        $this->app['form.type.guessers'] = function ($app) {
            return array();
        };

        $this->app['form.extension.csrf'] = function ($app) {
            if (isset($app['translator'])) {
                return new CsrfExtension($app['sf.csrf.token_manager'], $app['translator']);
            }
            return new CsrfExtension($app['sf.csrf.token_manager']);
        };

        $this->app['form.extensions'] = function ($app) {
            $extensions = array(
                new HttpFoundationExtension(),
            );

            // Csrf token integration
            if (isset($app['sf.csrf.token_manager'])) {
                $extensions[] = $app['form.extension.csrf'];
            }

            // Symfony validator integration
            if (isset($app['sf.validator'])) {
                $extensions[] = new FormValidatorExtension($app['sf.validator']);
            }

            // Doctrine integration
            if(class_exists('Symfony\\Bridge\\Doctrine\\Form\\DoctrineOrmExtension')){
                $extensions[] = new DoctrineOrmExtension($app['registry']);
            }

            return $extensions;
        };

        $this->app['form.resolved_type_factory'] = function ($app) {
            return new ResolvedFormTypeFactory();
        };

        $this->app['form.factory'] = function ($app) {
            return Forms::createFormFactoryBuilder()
                ->addExtensions($app['form.extensions'])
                ->addTypes($app['form.types'])
                ->addTypeExtensions($app['form.type.extensions'])
                ->addTypeGuessers($app['form.type.guessers'])
                ->setResolvedTypeFactory($app['form.resolved_type_factory'])
                ->getFormFactory()
                ;
        };
    }

    protected function registerTwigFormExtension()
    {
        $this->app['twig.form.templates'] = array('form_div_layout.html.twig');
        $this->app['twig.form.engine'] = function ($app) {
            return new TwigRendererEngine($app['twig.form.templates']);
        };
        $this->app['twig.form.renderer'] = function ($app) {
            return new TwigRenderer($app['twig.form.engine'], $app['sf.csrf.token_manager']);
        };

        // Make form extension for twig available as a service
        $this->app['twig.extension.form'] = new FormExtension($this->app['twig.form.renderer']);

        $reflected = new \ReflectionClass('Symfony\Bridge\Twig\Extension\FormExtension');
        $path = dirname($reflected->getFileName()).'/../Resources/views/Form';
        $this->app['twig.loader']->addLoader(new \Twig_Loader_Filesystem($path));
    }

    protected function registerCsrf()
    {
        $this->app['sf.csrf.token_manager'] = function ($app) {
            return new CsrfTokenManager($app['sf.csrf.token_generator'], $app['sf.csrf.token_storage']);
        };
        $this->app['sf.csrf.token_storage'] = function ($app) {
            // TODO: integrate csrf token manager with laravel session
//            if (isset($app['session'])) {
//                return new SessionTokenStorage($app['session'], $app['sf.csrf.session_namespace']);
//            }
            return new NativeSessionTokenStorage($app['sf.csrf.session_namespace']);
        };
        $this->app['sf.csrf.token_generator'] = function ($app) {
            return new UriSafeTokenGenerator();
        };
        $this->app['sf.csrf.session_namespace'] = '_csrf';
    }

    public function registerValidator()
    {
        $this->app['sf.validator'] = function ($app) {
            return $app['sf.validator.builder']->getValidator();
        };
        $this->app['sf.validator.builder'] = function ($app) {
            $builder = Validation::createValidatorBuilder();
            $builder->setConstraintValidatorFactory($app['sf.validator.validator_factory']);
            $builder->setTranslationDomain('sf.validators');
            $builder->addObjectInitializers($app['sf.validator.object_initializers']);
            $builder->setMetadataFactory($app['sf.validator.mapping.class_metadata_factory']);
            if (isset($app['translator'])) {
                $builder->setTranslator($app['translator']);
            }
            return $builder;
        };
        $this->app['sf.validator.mapping.class_metadata_factory'] = function ($app) {
            // LoaderChain allows validator to try validate model using StaticMethodLoader first then fallback to AnnotationLoader
            // StaticMethodLoader allows validator to validate model based on metadata given in static function loadValidatorMetadata
            // AnnotationLoader allows validator to validate model annotation (e.g: @Assert\NotBlank)
            $chainLoader =  new LoaderChain([new StaticMethodLoader(), new AnnotationLoader(new AnnotationReader())]);
            return new LazyLoadingMetadataFactory($chainLoader);
        };
        $this->app['sf.validator.validator_factory'] = function ($app){
            return new ConstraintValidatorFactory($app, $app['sf.validator.validator_service_ids']);
        };
        $this->app['sf.validator.object_initializers'] = function ($app) {
            return array();
        };
        $this->app['sf.validator.validator_service_ids'] = array();
    }
}