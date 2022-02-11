<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart")
     */
    public function index(SessionInterface $session,ProduitRepository $repoP): Response
    {
        $panier=$session->get("panier",[]);
        $panierWithData=[];
        $total=0;
        foreach($panier as $id=>$quantite){
            $panierWithData[]=[
                'produit' => $repoP->find($id),
                'quantite' => $quantite
            ];
            $totalI=$repoP->find($id)->getPrix()*$quantite;
            $total+=$totalI;
        }
        //dd($panierWithData);
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'items' => $panierWithData,
            'total' => $total
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add")
     */
    public function addToCart($id,SessionInterface $session){
        $panier=$session->get("panier",[]);
        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id]=1;
        }
        $session->set("panier",$panier);
        return $this->redirectToRoute("cart");
    }

    /**
     * @Route("/cart/update",name="cart_update")
     */
    public function updateCart(Request $request,SessionInterface $session){
        //dd($request);
        $panier=$session->get("panier",[]);
        foreach($request->request as $id=>$quantite){
            //dd(intval($quantite));
            $panier[$id]=intval($quantite);
            if(intval($quantite)<=0){
                unset($panier[$id]);
            }
        }
        $session->set('panier',$panier);

        return $this->redirectToRoute("cart");
    }

    /**
     * @Route("/cart/remove/{id}",name="cart_remove")
     */
    public function removeFromCart(Request $request,SessionInterface $session,$id){
        $panier=$session->get("panier",[]);
        if(!empty($panier[$id])){
            unset($panier[$id]);
        }
        $session->set('panier',$panier);
        return $this->redirectToRoute("cart");
    }
}
