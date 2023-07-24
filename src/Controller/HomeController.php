<?php

namespace App\Controller;

use App\Repository\MaintenanceRepository;
use App\Repository\SlideRepository;
use App\Service\UserActivityLogger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private MaintenanceRepository $maintenanceRepository,
        private SlideRepository $slideRepository
    )
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        if ($this->maintenanceRepository->findOneBy(['statut' => true]))
            return $this->redirectToRoute('app_frontend_maintenance_index',[],Response::HTTP_SEE_OTHER);

        return $this->render('frontend/home.html.twig',[
            'slides' => $this->slideRepository->findBy(['statut' => true],['id'=>"DESC"])
        ]);
    }

    public function lienActif(Request $request)
    {
        $currentRoute = $request->attributes->get('_route');
    }
}
