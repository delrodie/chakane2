<?php

namespace App\Controller\Backend;

use App\Repository\AllRepository;
use App\Service\UserActivityLogger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard')]
class DashboardController extends AbstractController
{
    public function __construct(
        private AllRepository $allRepository
    )
    {
    }

    #[Route('/', name: 'app_backend_dashboard_index')]
    public function index()
    {

        return $this->render('backend/dashboard.html.twig',[
            'devise' => $this->allRepository->allCache('devise', true)
        ])->setSharedMaxAge(3600);
    }


}