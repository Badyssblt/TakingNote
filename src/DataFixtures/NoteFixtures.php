<?php

namespace App\DataFixtures;

use App\Entity\Note;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class NoteFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setName($faker->name());
            $user->setEmail($faker->email());
            $user->setPassword($faker->password());
            $manager->persist($user);
        }

        for ($i = 0; $i < 10; $i++) {
            $note = new Note();
            $user->setName($faker->name());
            $user->setEmail($faker->email());
            $user->setPassword($faker->password());
            $manager->persist($user);
        }

        $manager->flush();
    }
}
