<?php

namespace App\Controller\Frontend;

use App\Entity\Produit;
use App\Repository\AllRepository;
use App\Service\AllCaches;
use Flasher\Prime\Flasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/boutique')]
class ProduitController extends AbstractController
{
    public function __construct(
        private AllRepository $allRepository,
        private AllCaches $allCaches,
        private CsrfTokenManagerInterface $csrfTokenManager,
        private Flasher $flasher
    )
    {
    }



    #[Route('/', name: 'app_frontend_produit_panier', methods: ['POST'])]
    public function panier(Request $request)
    {
        $devise = $request->getSession()->get('devise');

        // Vérification de l'authenticité du formulaire à traiter
        $submittedToken = $request->get('_token');
        $produitByRequest = $this->allRepository->getOneProduit($request->get('produit_slug'));
        $produit = $this->allRepository->getProduitWithDevise($produitByRequest);

        // Recuperation le CsrfTokenManager
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken($produit['slug'], $submittedToken))){
            sweetalert()->addWarning("Veuillez reprendre l'ajout du produit au panier");
            return $this->redirect('/boutique/'.$produit['slug']);
        }

        $qteRequest = (int) $request->get('quantite');
        $btnRequest = $request->get('btn'); //dd($produit);

        if ($btnRequest === 'ajouter' && $qteRequest > $produit['stock']){
            sweetalert()->addWarning("Désolé, il ne rest en stock que {$produit['stock']} produit(s). Voulez-vous toujours ajouter ce produit dans le panier?");
            return $this->redirect('/boutique/'.$produit['slug']);
        }

        $panier = $request->getSession()->get('panier', []);

        // Ajout du produit au panier
        $panier[$produit['id']] = [
            'quantite' => $qteRequest,
            'solde' => (int) $produit['solde'] * $qteRequest,
            'montant' => (float) $produit['montant'] * $qteRequest,
            'slug' => $produit['slug'],
            'id' => $produit['id'],
            'poids' => (float) $produit['poids'] * $qteRequest
        ];

        $request->getSession()->set('panier', $panier);
        $request->getSession()->set('btnSubmit', true);

//        $produit = $this->allRepository->getOneProduit($slug);
        $similaire = "{$produitByRequest->getSlug()}-{$devise}";
        return $this->render('frontend/produit_details.html.twig', [
            'produit' => $this->allRepository->getProduitWithDevise($produitByRequest),
            'similaires' => $this->allRepository->getProduitSimilaire($produitByRequest->getSlug(), $similaire, true),
//            'btnSumbit' => true
        ]);
    }

    #[Route('/{slug}', name: 'app_frontend_produit_details',methods: ['GET'])]
    public function details(Request $request, $slug): Response
    { //dd($slug);
        $devise = $request->getSession()->get('devise');

        $viewData = [
            'page' => null,
            'titre' => '',
            'categorie' => '',
            'produits' => [],
        ];

        if (in_array($slug, ['femmes', 'hommes', 'enfants', 'sacs', 'accessoires'])) {
            $viewData['produits'] = $this->allCaches->paginationProduitsByCategorie("{$slug}-{$devise}", 10);
            $viewData['page'] = $slug;
            $viewData['titre'] = "Catégories {$slug}";
        }elseif (in_array($slug, ['vetements', 'sacs-et-accessoires'])) {
            $viewData['produits'] = $this->allCaches->paginationProduistByType($slug, 10);
            $viewData['page'] = $slug;
            $viewData['titre'] = "Catégories {$slug}";
        } elseif ($slug === 'nouveaux-produits') {
            $viewData['produits'] = $this->allCaches->paginationDiversesRechercheProduits('newsProduits', 10);
            $viewData['categorie'] = "Nouveaux produits";
            $viewData['titre'] = "Nouveauté";
        } elseif ($slug === 'tous-les-produits') {
            $viewData['produits'] = $this->allCaches->paginationDiversesRechercheProduits('flagProduits',10);
            $viewData['categorie'] = "Tous les produits";
            $viewData['titre'] = "Tous les produits";
        } else {
            $produit = $this->allRepository->getOneProduit($slug);
            $similaire = "{$produit->getSlug()}-{$devise}";
            return $this->render('frontend/produit_details.html.twig', [
                'produit' => $this->allRepository->getProduitWithDevise($produit),
                'similaires' => $this->allRepository->getProduitSimilaire($produit->getSlug(), $similaire, true), 0153150116
            ]);
        }

        return $this->render('frontend/produits.html.twig', $viewData);
    }

}