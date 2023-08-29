<?php

namespace App\Controller;

use App\Repository\AllRepository;
use App\Repository\MaintenanceRepository;
use App\Repository\SlideRepository;
use App\Service\UserActivityLogger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private AllRepository $allRepository
    )
    {
    }

    #[Route('/', name: 'app_home')]
    #[Cache(expires: '+600 seconds', maxage: 3600, public: true, mustRevalidate: true)]
    public function index(Request $request): Response
    {
        if ($this->allRepository->allCache('maintenance'))
            return $this->redirectToRoute('app_frontend_maintenance_index',[],Response::HTTP_SEE_OTHER);

        // Nouveaux produits
        $news = $this->allRepository->allCache('newsProduits');
        $newsProduit=[];
        foreach ($news as $new){
            $newsProduit[] = $this->allRepository->getProduitWithDevise($new);
        }

        // Produits de la boutique
        $boutiques = $this->allRepository->allCache('flagProduits');
        $produit=[];
        foreach ($boutiques as $boutique){
            $produit[] = $this->allRepository->getProduitWithDevise($boutique);
        }



        return $this->render('frontend/home.html.twig',[
            'collections' => $this->allRepository->allCache('collections'),
            'news_produits' => $newsProduit,
            'produits' => $produit,
            'creations' => $this->allRepository->getRandomCreation(),
        ]);
    }

}
