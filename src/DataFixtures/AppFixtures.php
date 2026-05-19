<?php

namespace App\DataFixtures;

use App\Entity\Category;
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

        $category1 = new Category();
        $category1->setName('house');
        $category1->setNameFr('Maison');

        $category2 = new Category();
        $category2->setName('castle');
        $category2->setNameFr('Châteaux');

        $category3 = new Category();
        $category3->setName('nature');
        $category3->setNameFr('Nature');

        $category4 = new Category();
        $category4->setName('redstone');
        $category4->setNameFr('Redstone');

        $category5 = new Category();
        $category5->setName('boat');
        $category5->setNameFr('Bateaux');

        $category6 = new Category();
        $category6->setName('medieval');
        $category6->setNameFr('Médiéval');

        $category7 = new Category();
        $category7->setName('modern');
        $category7->setNameFr('Moderne');


        $manager->persist($category1);
        $manager->persist($category2);
        $manager->persist($category3);
        $manager->persist($category4);
        $manager->persist($category5);
        $manager->persist($category6);
        $manager->persist($category7);


        $manager->flush();
    }
}
