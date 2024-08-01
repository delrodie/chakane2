<?php

namespace App\Controller\Frontend;

use App\Entity\Panier;
use App\Repository\AllRepository;
use App\Service\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Locale;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/panier')]
class PanierController extends AbstractController
{
    public function __construct(
        private AllRepository $allRepository,
        private Utility $utility,
        private RequestStack $requestStack,
        private CsrfTokenManagerInterface $csrfTokenManager,
        private EntityManagerInterface $entityManager
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

        $submittedToken = $request->get('_csrf_token_confirmation');
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('confirmation', $submittedToken))) {
            sweetalert()->addWarning("Veuillez selectionner l'adresse de livraison");
            return $this->redirectToRoute('app_frontend_panier_livraison');
        }

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

        // Recherche du montant de la livraison selon l'adresse
        $montantLivraison=0;

        // Montant de la déduction relativement au coupon
        $deduction = 0;
        $verse = 0;
        $montantTotal = $sousTotal + $montantLivraison;
        $nap = $montantTotal - $deduction;
        $reste = $nap - $verse;

        // Enregistrement du panier
        $panier = New Panier();
        $panier->setProduits($produits);
        $panier->setSousTotal($sousTotal);
        $panier->setMontantLivraison($montantLivraison);
        $panier->setMontantTotal($montantTotal);
        $panier->setDeduction($deduction);
        $panier->setNap($nap);
        $panier->setVerse($verse);
        $panier->setReste($reste);
        $panier->setAdresse($this->allRepository->getAdresseById($request->get('livraison')));
        $panier->setClient($this->allRepository->getClientByUser($this->getUser()));

        $this->entityManager->persist($panier);
        $this->entityManager->flush();

//        $request->getSession()->set('panier', '');

        return $this->render('frontend/paiement.html.twig',[
            'produits' => $produits,
            'livraison' => $montantLivraison,
            'nap' => $nap
        ]);

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