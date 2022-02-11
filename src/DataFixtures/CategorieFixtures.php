<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Complement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategorieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /*// $product = new Product();
        // $manager->persist($product);
        $categories=["Boisson","Accompagnement"];

        foreach($categories as $cat){
            $categorie=new Categorie;
            $categorie->setNom($cat)
                      ->setSlug("slurp");
            $manager->persist($categorie);

            for ($i=1; $i <4 ; $i++) { 
                $compl=new Complement();
                $compl->setCategorie($categorie)
                      ->setNom($cat.$i)
                      ->setPrix(5000)
                      ->setImage("https://www.b2b-infos.com/wp-content/uploads/Fast-food-en-France.jpg");
                $manager->persist($compl);
            }
        }
        


        $manager->flush();*/
    }
}
