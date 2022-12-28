<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class'=> 'bg-transparent border-teal-500 border-2 w-full text-gray-700 mr-3 py-1 px-2 leading-tight focus:outline-none focus:bg-white focus:border-teal-700'
                ],
                'label' => 'E-mail'
            ])
            ->add('lastname', TextType::class, [
                'attr' => [
                    'class'=> 'bg-transparent border-teal-500 border-2 w-full text-gray-700 mr-3 py-1 px-2 leading-tight focus:outline-none focus:bg-white focus:border-teal-700'
                ],
                'label' => 'Nom'
            ])
            ->add('firstname', TextType::class, [
                'attr' => [
                    'class'=> 'bg-transparent border-teal-500 border-2 w-full text-gray-700 mr-3 py-1 px-2 leading-tight focus:outline-none focus:bg-white focus:border-teal-700'
                ],
                'label' => 'Prénom'
            ])
            ->add('address', TextType::class, [
                'attr' => [
                    'class'=> 'bg-transparent border-teal-500 border-2 w-full text-gray-700 mr-3 py-1 px-2 leading-tight focus:outline-none focus:bg-white focus:border-teal-700'
                ],
                'label' => 'Adresse'
            ])
            ->add('zipcode', TextType::class, [
                'attr' => [
                    'class'=> 'bg-transparent border-teal-500 border-2 w-full text-gray-700 mr-3 py-1 px-2 leading-tight focus:outline-none focus:bg-white focus:border-teal-700'
                ],
                'label' => 'Code postal'
            ])
            ->add('city', TextType::class, [
                'attr' => [
                    'class'=> 'bg-transparent border-teal-500 border-2 w-full text-gray-700 mr-3 py-1 px-2 leading-tight focus:outline-none focus:bg-white focus:border-teal-700'
                ],
                'label' => 'Ville'
            ])
            ->add('RGPDConsent', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
                'label' => 'J\'accepte les conditions générales d\'utilisation'
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class'=> 'bg-transparent border-teal-500 border-2 w-full text-gray-700 mr-3 py-1 px-2 leading-tight focus:outline-none focus:bg-white focus:border-teal-700'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'label' => 'Mot de passe'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
