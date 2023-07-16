<?php

namespace App\Controller;

use App\Repository\MaintenanceRepository;
use App\Service\UserActivityLogger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private MaintenanceRepository $maintenanceRepository
    )
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        if ($this->maintenanceRepository->findOneBy(['statut' => true]))
            return $this->redirectToRoute('app_frontend_maintenance_index',[],Response::HTTP_SEE_OTHER);

        return $this->render('frontend/home.html.twig');
    }
}
