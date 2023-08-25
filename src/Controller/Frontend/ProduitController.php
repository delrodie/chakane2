<?php

namespace App\Controller\Frontend;

use App\Entity\Produit;
use App\Repository\AllRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/article')]
class ProduitController extends AbstractController
{
    public function __construct(private AllRepository $allRepository)
    {
    }

    #[Route('/{slug}', name: 'app_frontend_produit_details',methods: ['GET'])]
    public function details(Request $request, Produit $produit): Response
    {
        $devise = $request->getSession()->get('devise');
        $similaire = "{$produit->getSlug()}-{$devise}" ;
        return $this->render('frontend/produit_details.html.twig',[
            'produit' => $this->allRepository->getProduitWithDevise($produit),
            'similaires' => $this->allRepository->getProduitSimilaire($similaire)
        ]);
    }
}