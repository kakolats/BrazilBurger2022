<?php

namespace App\Controller;

use DateTime;
use DateTimeImmutable;
use App\Entity\Commande;
use App\Entity\DetailCommande;
use App\Repository\ClientRepository;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
        $commande -> setStatut('En ATTENTE');
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
