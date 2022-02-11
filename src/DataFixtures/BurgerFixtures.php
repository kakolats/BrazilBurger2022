<?php

namespace App\DataFixtures;

use App\Entity\Burger;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Twig\Node\SetNode;

class BurgerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
       /* // $product = new Product();
        // $manager->persist($product);
        for ($i=1; $i <4 ; $i++) { 
            $burger=new Burger();
            $burger->setNom("Burger".$i)
                   ->setPrix(2500)
                   ->setImage("https://www.b2b-infos.com/wp-content/uploads/Fast-food-en-France.jpg")
                   ->setDescription("Ceci est la description du burger");
            $manager->persist($burger);
        }
        $manager->flush();*/
    }
}
