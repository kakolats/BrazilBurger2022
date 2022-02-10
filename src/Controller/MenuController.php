<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MenuController extends AbstractController
{
    /**
     * @Route("/menu", name="menu")
     */
    public function index(): Response
    {
        return $this->render('menu/index.html.twig', [
            'controller_name' => 'MenuController',
        ]);
    }

    public function addMenu(Menu $menu,ObjectManager $manager,MenuRepository $menuR){
        $manager->persist($menu);
        $manager->flush();
        $menus=$menuR->findAll();
        $this->redirectToRoute("/menu",[
            "menus" =>$menus
        ]);
    }
}
