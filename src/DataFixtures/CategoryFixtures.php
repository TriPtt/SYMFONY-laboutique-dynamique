<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryFixtures extends Fixture
{
    private $count = 1;

    public function __construct(private SluggerInterface $slugger){}

    public function load(ObjectManager $manager): void
    {
        $parent = $this->createCategory('Mode', null, $manager);

        $this->createCategory('Vêtements', $parent, $manager);
        $this->createCategory('Chaussures', $parent, $manager);
        $this->createCategory('Bijoux', $parent, $manager);

        $parent = $this->createCategory('Maison', null, $manager);

        $this->createCategory('Décoration', $parent, $manager);
        $this->createCategory('Electroménager', $parent, $manager);
        $this->createCategory('Jardinage', $parent, $manager);
        
        $manager->flush();
    }

    public function createCategory($name, $parent = null, ObjectManager $manager)
    {
        $category = new Category();
        $category->setName($name);
        $category->setSlug($this->slugger->slug($category->getName())->lower());
        $category->setParent($parent);
        $manager->persist($category);

        $this->addReference('category_' . $this->count, $category);
        $this->count++;

        return $category;
    }
}
