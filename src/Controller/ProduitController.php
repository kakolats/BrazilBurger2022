<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Burger;
use App\Entity\Complement;
use App\Repository\MenuRepository;
use App\Repository\BurgerRepository;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use App\Repository\ComplementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    /**
     * @Route("/produit", name="produit")
     */
    public function index(ProduitRepository $repoP,BurgerRepository $repoB,MenuRepository $repoM): Response
    {
        $burgers=$repoB->findBy(["isArchive"=>false]);
        $menus=$repoM->findBy(["isArchive"=>false]);

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
    public function burger_add(BurgerRepository $repoP,Request $request,EntityManagerInterface $em
    ,ValidatorInterface $validator)
    {
        $burger=new Burger();
        $burger->setNom($request->request->get("nom"))
               ->setPrix(intval($request->request->get("prix")))
               ->setDescription($request->request->get("description"));
        $image=$request->files->get("picture");
        $imageName=uniqid().'.'.$image->guessExtension();
        $image->move($this->getParameter("images_directory"),$imageName);
        $burger->setImage($imageName);
        $burger->setIsArchive(false);
        $errors=$validator->validate($burger);
        if(count($errors)>0){
            dd($errors);
        }else{
            $em->persist($burger);
            $em->flush();
        }
        return $this->redirectToRoute("burger_gestion");
        
    }


    /**
     * @Route("gestionnaire/complements/add", name="complement_add")
     */
    public function complement_add(ComplementRepository $repoP,CategorieRepository $repoC,Request $request,EntityManagerInterface $em
    ,ValidatorInterface $validator)
    {
        //dd($request);
        $complement=new Complement(); 
        $complement->setNom($request->request->get("name"))
                   ->setPrix(intval($request->request->get("prix")));
        $categorie=$repoC->find($request->request->get("categorie"));
        $complement->setCategorie($categorie);
        $image=$request->files->get("picture");
        $imageName=uniqid().'.'.$image->guessExtension();
        $image->move($this->getParameter("images_directory"),$imageName);
        $complement->setImage($imageName);
        $complement->setIsArchive(false);
        $errors=$validator->validate($complement);
        if(count($errors)>0){
            dd($errors);
        }else{
            //dd($complement);
            $em->persist($complement);
            $em->flush();
        }
        return $this->redirectToRoute("complement_gestion");    
    }

     /**
     * @Route("gestionnaire/complements/archive/{id}", name="complement_archive")
     */
    public function complement_archive($id,ComplementRepository $repoC,EntityManagerInterface $em){
        $complement=$repoC->find($id);
        if($complement->getIsArchive()){
            $complement->setIsArchive(false);
        }else{
            $complement->setIsArchive(true);
        }
        $em->persist($complement);
        $em->flush();
        return $this->redirectToRoute("complement_gestion");
    }

    /**
     * @Route("gestionnaire/complements/update", name="complement_update")
     */
    public function complement_update(ComplementRepository $repoP,CategorieRepository $repoC,Request $request,EntityManagerInterface $em
    ,ValidatorInterface $validator)
    {
        $complement=$repoP->find($request->request->get("id")); 
        $complement->setNom($request->request->get("name"))
                   ->setPrix(intval($request->request->get("prix")));
        $categorie=$repoC->find($request->request->get("categorie"));
        $complement->setCategorie($categorie);
        $image=$request->files->get("picture");
        if($image){
            $imageName=uniqid().'.'.$image->guessExtension();
            $image->move($this->getParameter("images_directory"),$imageName);
            unlink($this->getParameter("images_directory")."/".$complement->getImage());
            $complement->setImage($imageName);
        }
        $errors=$validator->validate($complement);
        if(count($errors)>0){
            dd($errors);
        }else{
            $em->persist($complement);
            $em->flush();
        }
        return $this->redirectToRoute("complement_gestion");    
    }

    /**
     * @Route("gestionnaire/burgers/update", name="burger_update")
     */
    public function burger_update(BurgerRepository $repoP,Request $request,EntityManagerInterface $em
    ,ValidatorInterface $validator)
    {
        $burger=$repoP->find($request->request->get("id"));
        $burger->setNom($request->request->get("nom"))
               ->setPrix(intval($request->request->get("prix")))
               ->setDescription($request->request->get("description"));
        $image=$request->files->get("picture");
        if($image){
            //dd($image);
            $imageName=uniqid().'.'.$image->guessExtension();
            $image->move($this->getParameter("images_directory"),$imageName);
            unlink($this->getParameter("images_directory")."/".$burger->getImage());
            $burger->setImage($imageName);
        } 
        $errors=$validator->validate($burger);
        if(count($errors)>0){
            dd($errors);
        }else{
            $em->persist($burger);
            $em->flush();
        }
        return $this->redirectToRoute("burger_gestion");
        
    }

    /**
     * @Route("gestionnaire/burgers/edit/{id}", name="burger_edit")
     */
    public function burger_edit(BurgerRepository $repoB, Burger $burgerSelected): Response
    {

        $produits=$repoB->findAll();
        //dd($complements);
        return $this->render('produit/gestionnaire.burger.html.twig', [
            'controller_name' => 'ProduitController',
            'produits' => $produits,
            'burgerSelected' => $burgerSelected
        ]);
    }

    /**
     * @Route("gestionnaire/complements/edit/{id}", name="complement_edit")
     */
    public function complement_edit(CategorieRepository $repoC,ComplementRepository $repoP,
    Complement $complementSelected): Response
    {

        $categories=$repoC->findAll();
        $produits=$repoP->findAll();
        return $this->render('produit/gestionnaire.complement.html.twig', [
            'controller_name' => 'ProduitController',
            'categories' => $categories,
            'produits' => $produits,
            'complementSelected' => $complementSelected
        ]);
    }



    /**
     * @Route("gestionnaire/burgers/archive/{id}", name="burger_archive")
     */
    public function burger_archive($id,BurgerRepository $repoB,EntityManagerInterface $em){
        $burger=$repoB->find($id);
        if($burger->getIsArchive()){
            $burger->setIsArchive(false);
        }else{
            $burger->setIsArchive(true);
        }
        $em->persist($burger);
        $em->flush();
        return $this->redirectToRoute("burger_gestion");
    }


    /**
     * @Route("gestionnaire/menus", name="menu_gestion")
     */
    public function menu_gestion(MenuRepository $repoP,BurgerRepository $repoB,ComplementRepository $repoC): Response
    {
        $burgers=$repoB->findAll();
        $produits=$repoP->findAll();
        $complements=$repoC->findAll();
        //dd($complements);
        return $this->render('produit/gestionnaire.menu.html.twig', [
            'controller_name' => 'ProduitController',
            'produits' => $produits,
            'burgers' => $burgers,
            'complements' => $complements
        ]);
    }

     /**
     * @Route("gestionnaire/menus/add", name="menu_gestion_add")
     */
    public function menu_gestion_add(Request $request,BurgerRepository $repoB,ComplementRepository $repoC,
    EntityManagerInterface $em,ValidatorInterface $validator)
    {
        dd($request);
        $prix=0;
        $burger=$repoB->find($request->request->get("burger"));
        $prix+=$burger->getPrix();
        $menu =new Menu();
        $menu->setNom($request->request->get("nom"));
        $menu->setBurger($burger);
        foreach((array)$request->request->get("complements") as $idC){
            $complement=$repoC->find($idC);
            $prix+=$complement->getPrix();
            $menu->addComplement($complement);
        }
        $menu->setPrix($prix);
        $image=$request->files->get("image");
        $imageName=uniqid().'.'.$image->guessExtension();
        $image->move($this->getParameter("images_directory"),$imageName);
        $menu->setImage($imageName);
        $menu->setIsArchive(false);
        $errors=$validator->validate($menu);
        if(count($errors)>0){
            dd($errors);
        }else{
            $em->persist($menu);
            $em->flush();
        }
        return $this->redirectToRoute("menu_gestion");
    }

    

    /**
     * @Route("gestionnaire/menus/edit/{id}", name="menu_gestion_edit")
     */
    public function menu_gestion_edit(BurgerRepository $repoB,MenuRepository $repoM,
    Menu $menuSelected,ComplementRepository $repoC,Request $request): Response
    {
        $burgers=$repoB->findAll();
        $produits=$repoM->findAll();
        $complements=$repoC->findAll();
        //dd($complements);
        return $this->render('produit/gestionnaire.menu.html.twig', [
            'controller_name' => 'ProduitController',
            'produits' => $produits,
            'burgers' => $burgers,
            'complements' => $complements,
            'menuSelected' => $menuSelected
        ]);
    }

    /**
     * @Route("gestionnaire/menus/update", name="menu_gestion_update")
     */
    public function menu_gestion_update(MenuRepository $repoP,BurgerRepository $repoB,ComplementRepository $repoC,
    Request $request,ValidatorInterface $validator,EntityManagerInterface $em): Response
    {
        $menuSelected=$repoP->find($request->request->get("id"));

        $prix=0;
        $burger=$repoB->find($request->request->get("burger"));
        $prix+=$burger->getPrix();
        $menuSelected->emptyComplements();
        $menuSelected->setNom($request->request->get("nom"));
        $menuSelected->setBurger($burger);
        foreach((array)$request->request->get("complements") as $idC){
            $complement=$repoC->find($idC);
            $prix+=$complement->getPrix();
            $menuSelected->addComplement($complement);
        }
        $menuSelected->setPrix($prix);

        $image=$request->files->get("image");
        if($image){
            //dd($image);
            $imageName=uniqid().'.'.$image->guessExtension();
            $image->move($this->getParameter("images_directory"),$imageName);
            unlink($this->getParameter("images_directory")."/".$menuSelected->getImage());
            $menuSelected->setImage($imageName);
        }  
        $errors=$validator->validate($menuSelected);
        if(count($errors)>0){
            dd($errors);
        }else{
            //dd($menuSelected);
            $em->persist($menuSelected);
            $em->flush();
        }
        //dd($complements);
        return $this->redirectToRoute("menu_gestion");
    }

    /**
     * @Route("gestionnaire/menus/archive/{id}", name="menu_gestion_archive")
     */
    public function menu_gestion_archive($id,MenuRepository $repoM,EntityManagerInterface $em){
        $menu=$repoM->find($id);
        if($menu->getIsArchive()){
            $menu->setIsArchive(false);
        }else{
            $menu->setIsArchive(true);
        }
        $em->persist($menu);
        $em->flush();
        return $this->redirectToRoute("menu_gestion");
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
