<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private SluggerInterface $slugger
    ){}

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@admin.fr');
        $admin->setLastname('Petit');
        $admin->setFirstname('Tristan');
        $admin->setAddress('1 rue de la paix');
        $admin->setZipcode('75000');
        $admin->setCity('Paris');
        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, 'admin')
        );
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $faker = Faker\Factory::create('fr_FR');

        for($user = 1; $user <= 5; $user++){
            $newUser = new User();
            $newUser->setEmail($faker->email);
            $newUser->setLastname($faker->lastName);
            $newUser->setFirstname($faker->firstName);
            $newUser->setAddress($faker->streetAddress);
            $newUser->setZipcode(str_replace(' ', '', $faker->postcode));
            $newUser->setCity($faker->city);
            $newUser->setPassword(
                $this->passwordHasher->hashPassword($newUser, 'secret')
            );

            $manager->persist($newUser);
        }

        $manager->flush();
    }
}
