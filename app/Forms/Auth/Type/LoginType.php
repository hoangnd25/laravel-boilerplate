<?php

namespace App\Forms\Auth\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array(
                'label' => 'auth.form.email',
            ))
            ->add('plainPassword', PasswordType::class, array(
                'label' => 'auth.form.password'
            ))
            ->add('rememberMe', CheckboxType::class, array(
                'label' => 'auth.form.login.remember_me',
                'required' => false
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'auth.form.login.submit'
            ))
        ;
    }
}