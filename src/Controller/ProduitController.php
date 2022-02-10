<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use App\Repository\BurgerRepository;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use App\Repository\ComplementRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    /**
     * @Route("/produit", name="produit")
     */
    public function index(ProduitRepository $repoP,BurgerRepository $repoB,MenuRepository $repoM): Response
    {
        $burgers=$repoB->findAll();
        $menus=$repoM->findAll();

        return $this->render('produit/index.html.twig', [
            'controller_name' => 'ProduitController',
            'burgers' => $burgers,
            'menus' => $menus,
        ]);
    }

    /**
     * @Route("/produit/details/{idProd}", name="produit_details")
     */
    public function produit_details(ProduitRepository $repoP,$idProd): Response
    {
        $produit=$repoP->find($idProd);

        return $this->render('produit/details.produit.html.twig', [
            'controller_name' => 'ProduitController',
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("gestionnaire/burgers", name="burger_gestion")
     */
    public function burger_gestion(BurgerRepository $repoP): Response
    {
        $produits=$repoP->findAll();
        return $this->render('produit/gestionnaire.burger.html.twig', [
            'controller_name' => 'ProduitController',
            'produits' => $produits
        ]);
    }

    /**
     * @Route("gestionnaire/burgers/add", name="burger_add")
     */
    public function burger_add(BurgerRepository $repoP): Response
    {
        $produits=$repoP->findAll();
        return $this->render('produit/gestionnaire.burger.html.twig', [
            'controller_name' => 'ProduitController',
            'produits' => $produits
        ]);
    }



    /**
     * @Route("gestionnaire/menus", name="menu_gestion")
     */
    public function menu_gestion(MenuRepository $repoP): Response
    {
        $produits=$repoP->findAll();
        return $this->render('produit/gestionnaire.menu.html.twig', [
            'controller_name' => 'ProduitController',
            'produits' => $produits
        ]);
    }

    /**
     * @Route("gestionnaire/complements", name="complement_gestion")
     */
    public function complement_gestion(CategorieRepository $repoC,ComplementRepository $repoP): Response
    {
        $categories=$repoC->findAll();
        $produits=$repoP->findAll();
        return $this->render('produit/gestionnaire.complement.html.twig', [
            'controller_name' => 'ProduitController',
            'categories' => $categories,
            'produits' => $produits
        ]);
    }


}
