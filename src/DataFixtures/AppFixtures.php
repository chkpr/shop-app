<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Factory\CategoryFactory;
use App\Factory\ProductFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        CategoryFactory::createMany(10);

        ProductFactory::createMany(30, fn() => [
            'category' => CategoryFactory::random(),
        ]);
        }
}
