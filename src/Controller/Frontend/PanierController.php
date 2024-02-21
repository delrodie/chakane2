<?php

namespace App\Controller\Frontend;

use App\Repository\AllRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/panier')]
class PanierController extends AbstractController
{
    public function __construct(
        private AllRepository $allRepository
    )
    {
    }

    #[Route('/', name: 'app_frontend_panier_index')]
    public function index(Request $request)
    {
        $devise = $request->getSession()->get('devise');

        $panier = $request->getSession()->get('panier');

        $produits=[]; $i=0; $sousTotal=0;
        foreach ($panier as $element){
            $produit = $this->allRepository->getOneProduit($element['slug']);
            $produitDevise = $this->allRepository->getProduitWithDevise($produit);

            if ($produitDevise['solde']) {
                $montant = $produitDevise['solde'] * $element['quantite'];
            }
            else {
                $montant = $produitDevise['montant'] * $element['quantite'];
            }

            $produits[$i++] = [
                'produit' => $produitDevise,
                'quantite' => $element['quantite'],
                'montant' => $montant,
                'prix' => $element['montant']
            ];

            $sousTotal += $montant;
        }

        return $this->render('frontend/panier.html.twig',[
            'produits' => $produits,
            'sousTotal' => $sousTotal
        ]);
    }
}