<?php

namespace App\Controller\Frontend;

use App\Repository\AllRepository;
use App\Service\Utility;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Locale;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/panier')]
class PanierController extends AbstractController
{
    public function __construct(
        private AllRepository $allRepository,
        private Utility $utility,
        private RequestStack $requestStack
    )
    {
    }

    #[Route('/', name: 'app_frontend_panier_index')]
    public function index(Request $request): Response
    {
        $devise = $request->getSession()->get('devise');

//        $panier = $request->getSession()->get('panier');


        $produits=[]; $i=0; $sousTotal=0;
        foreach ($this->panierSession() as $element){
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

        // Mettre en session le lien de la livraison.
        $request->getSession()->set('redirectPath', 'app_frontend_panier_livraison');

        return $this->render('frontend/panier.html.twig',[
            'produits' => $produits,
            'sousTotal' => $sousTotal
        ]);
    }


    #[Route('/livraison', name: 'app_frontend_panier_livraison', methods: ['GET', 'POST'])]
    #[isGranted('ROLE_USER')]
    public function livraison(Request $request): Response
    {

//        $panier = $request->getSession()->get('panier');

//        if (!$this->panierSession()){
//            return $this->render('frontend/panier_vide.html.twig');
//        }

        $produits=[]; $i=0; $sousTotal=0;
        foreach ($this->panierSession() as $element){
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

        // suppression du lien de redirection
        $request->getSession()->set('redirectPath', '');

        return $this->render('frontend/livraison.html.twig',[
            'countries' => Countries::getNames(),
            'email' => $this->getUser()->getUserIdentifier(),
            'client' => $this->allRepository->getClientByUser($this->getUser()),
            'adresses' => $this->allRepository->getAdresseByUser($this->getUser()),
            'produits' => $produits,
            'sousTotal' => $sousTotal
        ]);
    }

    #[Route('/confirmation/commande', name: 'app_frontend_panier_confirmation', methods: ['GET','POST'])]
    #[isGranted('ROLE_USER')]
    public function confirmation(Request $request): Response
    {
        dd($this->panierSession());
    }


    public function panierSession()
    {
        $panier = $this->requestStack->getSession()->get('panier');

        if (!$panier){
            return $this->render('frontend/panier_vide.html.twig');
        }

        return $panier;
    }
}