<?php

namespace App\Controller;

use DateTime;
use DateTimeImmutable;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Entity\DetailCommande;
use App\Repository\ClientRepository;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DetailCommandeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController
{
    /**
     * @Route("/commande", name="commande")
     */
    public function index(): Response
    {
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }

    /**
     * @IsGranted("ROLE_GESTIONNAIRE")
     * @Route("/commande/all", name="commande_all")
     */
    public function getAllCommandes(CommandeRepository $repoC): Response{
        
        $commandes=$repoC->findAll();
        return $this->render('commande/gestionnaire.commandes.html.twig', [
            'controller_name' => 'CommandeController',
            'commandes' => $commandes,
            
        ]);
    }

    /**
     * @IsGranted("ROLE_GESTIONNAIRE")
     * @Route("/commande/all/edit/{id}", name="commande_edit")
     */
    public function commandeEdit(CommandeRepository $repoC,Commande $commandeSelected,
    DetailCommandeRepository $repoD): Response{
        $commandes=$repoC->findAll();
        $details=$repoD->findBy(["commande"=>$commandeSelected]);
        $produits=array();
        foreach($details as $item){
            $produits[]=[
                "quantite"=>$item->getQuantite(),
                "produit" =>$item->getProduit()->getNom()
            ];
        }
        //dd($produits);
        return $this->render('commande/gestionnaire.commandes.html.twig', [
            'controller_name' => 'CommandeController',
            'commandes' => $commandes,
            'commandeSelected' => $commandeSelected,
            'produits' => $produits
        ]);
    }

    /**
     * @IsGranted("ROLE_GESTIONNAIRE")
     * @Route("/commande/all/update", name="commande_update")
     */
    public function commandeUpdate(CommandeRepository $repoC,Request $request,EntityManagerInterface $em): Response{

        $commande=$repoC->find($request->request->get("id"));
        $commande->setStatut($request->request->get("etat"));
        if($request->request->get("etat")=="PAYE"){
            $commande->setIsPaid(true);
        }
        $em->persist($commande);
        $em->flush();
        return $this->redirectToRoute("commande_all");
    }

    public function getFullCart(SessionInterface $session,ProduitRepository $repoP): array
    {
        $cart = $session->get('panier',[]);
        $cartWithData=[];
        foreach ($cart as $id => $quantite) {  
            $cartWithData[]=[
                'item' => $repoP->find($id),
                'quantite' => $quantite
            ];
        }
        return $cartWithData;
    }

    public function getTotal(SessionInterface $session,ProduitRepository $productRepository): float
    {
        $cartWithData = $this->getFullCart($session, $productRepository);
        $total = 0;
        foreach ($cartWithData as $couple) {
            $total += $couple['item']->getPrix() * $couple['quantite'];
        }
        return $total;
    }

    /**
     * @IsGranted("ROLE_CLIENT")
     * @Route("/commande/client", name="commande_client")
     */
    public function showClientCommandes(CommandeRepository $repoC): Response
    {
        $user=$this->getUser();
        $commandes=$repoC->findBy(["client"=>$user]);
        return $this->render('commande/client.commandes.html.twig', [
            'controller_name' => 'CommandeController',
            'commandes' => $commandes
        ]);
    }

    /**
     * @IsGranted("ROLE_CLIENT")
     * @Route("/commande/add", name="commande_add")
     */
    public function addCommande(SessionInterface $session, ProduitRepository $productRepository, ClientRepository $cl, EntityManagerInterface $em)
    {
        $commande = new Commande();
        $commande -> setReference(uniqid());
        $commande -> setCreatedAt(new DateTimeImmutable());
        $commande -> setUpdatedAt(new DateTime());
        $commande -> setStatut('EN ATTENTE');
        $commande -> setIsPaid(false);
        $commande -> setTotal($this->getTotal($session, $productRepository));
        $commande -> setClient($this->getUser());
       
        $em ->persist($commande);

        $produitsPanier = $this -> getFullCart($session, $productRepository);
        foreach($produitsPanier as $prod){
            $detailCommande = new DetailCommande ();
            $detailCommande -> setQuantite($prod['quantite']);
            $detailCommande -> setCommande($commande);
            $detailCommande -> setProduit($prod['item']);
            $detailCommande -> setTotal($prod['quantite'] * $prod['item']->getPrix() );

            $em -> persist($detailCommande);
            $commande -> addDetailCommande($detailCommande);
        }
        $em ->persist($commande);
        $em -> flush();
        $session->set("panier",[]);
        return $this->redirectToRoute("produit");
    }
}
