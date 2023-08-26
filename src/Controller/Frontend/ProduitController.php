<?php

namespace App\Controller\Frontend;

use App\Entity\Produit;
use App\Repository\AllRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/boutique')]
class ProduitController extends AbstractController
{
    public function __construct(private AllRepository $allRepository)
    {
    }

    #[Route('/{slug}', name: 'app_frontend_produit_details',methods: ['GET'])]
    public function details(Request $request, $slug): Response
    {
        $devise = $request->getSession()->get('devise');

        $viewData = [
            'page' => null,
            'titre' => '',
            'categorie' => '',
            'produits' => [],
        ];

        if (in_array($slug, ['femmes', 'hommes', 'enfants', 'sacs', 'accessoires', 'sacs-accessoires'])) {
            $viewData['produits'] = $this->allRepository->getProduitsByCategorie("{$slug}-{$devise}");
            $viewData['page'] = $slug;
            $viewData['titre'] = "Catégories {$slug}";
        } elseif ($slug === 'nouveaux-produits') {
            $viewData['produits'] = $this->allRepository->allCache('newsProduits');
            $viewData['categorie'] = "Nouveaux produits";
            $viewData['titre'] = "Nouveauté";
        } elseif ($slug === 'tous-les-produits') {
            $viewData['produits'] = $this->allRepository->allCache('flagProduits');
            $viewData['categorie'] = "Tous les produits";
            $viewData['titre'] = "Tous les produits";
        } else {
            $produit = $this->allRepository->getOneProduit($slug);
            $similaire = "{$produit->getSlug()}-{$devise}";
            return $this->render('frontend/produit_details.html.twig', [
                'produit' => $this->allRepository->getProduitWithDevise($produit),
                'similaires' => $this->allRepository->getProduitSimilaire($similaire),
            ]);
        }

        return $this->render('frontend/produits.html.twig', $viewData);
    }

}