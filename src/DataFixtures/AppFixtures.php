<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Mcversion;
use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach (['ROLE_ADMIN', 'ROLE_USER'] as $code) {
            if ($manager->getRepository(Role::class)->findOneBy(['code' => $code]) !== null) {
                continue;
            }

            $role = new Role();
            $role->setCode($code);
            $role->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($role);
        }

        $categories = [
            'house' => 'Maison',
            'castle' => 'Châteaux',
            'nature' => 'Nature',
            'redstone' => 'Redstone',
            'boat' => 'Bateaux',
            'medieval' => 'Médiéval',
            'modern' => 'Moderne',
        ];

        foreach ($categories as $name => $nameFr) {
            $category = $manager->getRepository(Category::class)->findOneBy(['name' => $name]);

            if ($category === null) {
                $category = new Category();
                $category->setName($name);
                $manager->persist($category);
            }

            $category->setNameFr($nameFr);
        }

        for ($i = 0; $i < 27; $i++) {
            if ($manager->getRepository(Mcversion::class)->findOneBy(['number' => '1.'.$i]) !== null) {
                continue;
            }

            $version = new Mcversion() ;
            $version->setNumber('1.'.$i);
            $manager->persist($version);
        }

        $manager->flush();
    }
}
