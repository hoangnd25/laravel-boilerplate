<?php

namespace App\Forms\Auth\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordResetUpdateType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', HiddenType::class)
            ->add('token', HiddenType::class)
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options' => array('label' => 'auth.form.password'),
                'second_options' => array('label' => 'auth.form.password_confirmation'),
                'invalid_message' => trans('auth.error.reset_password.password_mismatch'),
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'auth.form.reset_password.submit'
            ))
        ;
    }
}