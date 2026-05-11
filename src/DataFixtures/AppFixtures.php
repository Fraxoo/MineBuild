<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $roleAdmin = new Role();
        $roleAdmin->setCode('ROLE_ADMIN');
        $roleAdmin->setCreatedAt(new \DateTimeImmutable());
        $roleUser = new Role();
        $roleUser->setCode('ROLE_USER');
        $roleUser->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($roleAdmin);
        $manager->persist($roleUser);

        $manager->flush();
    }
}
