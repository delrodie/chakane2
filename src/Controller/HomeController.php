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

//        dd($this->allRepository->cacheDiversesRechercheProduits('flagProduits'));

        return $this->render('frontend/home.html.twig',[
            'collections' => $this->allRepository->allCache('collections'),
            'news_produits' => $this->allRepository->cacheDiversesRechercheProduits('newsProduits', true),
            'produits' => $this->allRepository->cacheDiversesRechercheProduits('flagProduits', true),
            'creations' => $this->allRepository->getRandomCreation(),
        ]);
    }

}
