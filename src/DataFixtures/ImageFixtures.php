<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Image;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($image = 1; $image <= 20; $image++){
            $newImage = new Image();
            $newImage->setName($faker->image(null, 640, 480));
            $product = $this->getReference('product_' .rand(1, 10));
            $newImage->setProduct($product);

            $manager->persist($newImage);
        }

        $manager->flush();
    }

    public function getDependencies() : array
    {
        return [
            ProductFixtures::class,
        ];
    }
}
