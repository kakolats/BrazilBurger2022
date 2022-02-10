<?php

namespace App\DataFixtures;

use App\Entity\Menu;
use App\Repository\BurgerRepository;
use App\Repository\CategorieRepository;
use App\Repository\ComplementRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MenuFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        
    }
}
