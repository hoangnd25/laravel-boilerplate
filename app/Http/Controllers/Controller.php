<?php

namespace App\Http\Controllers;

use App\Facades\FormFactory;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Return name for default repository
     * @return string
     */
    protected function getDefaultRepositoryName(){
        return '';
    }

    /**
     * Convenient method for returning object repository
     * @param string $name
     */
    protected function getRepository($name = null){
        if($name === null)
            $name = $this->getDefaultRepositoryName();

        return EntityManager::getRepository($name);
    }

    /**
     * Creates and returns a Form instance from the type of the form.
     *
     * @param string $type    The fully qualified class name of the form type
     * @param mixed  $data    The initial data for the form
     * @param array  $options Options for the form
     *
     * @return Form
     */
    protected function createForm($type, $data = null, array $options = array())
    {
        return FormFactory::create($type, $data, $options);
    }
    /**
     * Creates and returns a form builder instance.
     *
     * @param mixed $data    The initial data for the form
     * @param array $options Options for the form
     *
     * @return FormBuilder
     */
    protected function createFormBuilder($data = null, array $options = array())
    {
        return FormFactory::createBuilder(FormType::class, $data, $options);
    }
}