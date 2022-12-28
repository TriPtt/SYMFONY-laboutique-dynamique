<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;
use Faker;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger){}

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($product = 1; $product <= 10; $product++){
            $newProduct = new Product();
            $newProduct->setName($faker->text(5));
            $newProduct->setSlug($this->slugger->slug($newProduct->getName())->lower());
            $newProduct->setDescription($faker->text());
            $newProduct->setPrice($faker->randomFloat(2, 10, 1000));
            $newProduct->setStock($faker->numberBetween(0, 10));

            $category = $this->getReference('category_' .rand(1, 8));
            $newProduct->setCategory($category);

            $this->setReference('product_' . $product, $newProduct);
            $manager->persist($newProduct);
        }

        $manager->flush();
    }
}
