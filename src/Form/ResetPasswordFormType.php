<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class, [
                'attr' => [
                    'class' => 'bg-transparent border-teal-500 border-2 w-full text-gray-700 mr-3 py-1 px-2 leading-tight focus:outline-none focus:bg-white focus:border-teal-700',
                    'placeholder' => 'Entrez votre nouveau mot de passe',
                ],
                'label' => 'Nouveau mot de passe',

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
