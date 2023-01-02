<?php

namespace App\Form;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'bg-transparent border-teal-500 border-2 w-full text-gray-700 mb-3 py-1 px-2 leading-tight focus:outline-none focus:bg-white focus:border-teal-700'
                ],
                'label' => 'Nom du produit'
            ])
            ->add('description', options: [
                'attr' => [
                    'class' => 'bg-transparent border-teal-500 border-2 w-full text-gray-700 mb-3 py-1 px-2 leading-tight focus:outline-none focus:bg-white focus:border-teal-700'
                ],
                'label' => 'Description'
            ])
            ->add('price', IntegerType::class, [
                'attr' => [
                    'class' => 'bg-transparent border-teal-500 border-2 w-full text-gray-700 mb-3 py-1 px-2 leading-tight focus:outline-none focus:bg-white focus:border-teal-700'
                ],
                'label' => 'Prix'
            ])
            ->add('stock', IntegerType::class, [
                'attr' => [
                    'class' => 'bg-transparent border-teal-500 border-2 w-full text-gray-700 mb-3 py-1 px-2 leading-tight focus:outline-none focus:bg-white focus:border-teal-700'
                ],
                'label' => 'Unités en stock'
            ])
            ->add('category', EntityType::class, [
                'class' => 'App\Entity\Category',
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'bg-transparent border-teal-500 border-2 w-full text-gray-700 mb-3 py-1 px-2 leading-tight focus:outline-none focus:bg-white focus:border-teal-700'
                ],
                'label' => 'Catégorie',
                'group_by' => 'parent.name',
                'query_builder' => function (CategoryRepository $cr) {
                    return $cr->createQueryBuilder('c')
                        ->where('c.parent IS NOT NULL')
                        ->orderBy('c.name', 'ASC');
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
