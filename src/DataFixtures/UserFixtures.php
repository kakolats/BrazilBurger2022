<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    private $encoder;
    public function __construct(UserPasswordHasherInterface $encoder){
        $this->encoder=$encoder;
    }
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $client=new Client();
        $client->setTelephone("77 777 77 77")
               ->setEmail("client1@gmail.com")
               ->setNom("Client")
               ->setPrenom("client");
        $plainPassword="1234";
        $passwordEncode= $this->encoder->hashPassword($client,$plainPassword);
        $client->setPassword($passwordEncode);
        $manager->persist($client);
        $manager->flush();
    }
}
