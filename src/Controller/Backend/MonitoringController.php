<?php

namespace App\Controller\Backend;

use App\Service\UserActivityLogger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/monitoring')]
class MonitoringController extends AbstractController
{
    public function __construct(
        private UserActivityLogger $userActivityLogger
    )
    {
    }

    #[Route('/', name: 'app_backend_monitoring_index')]
    public function index()
    {
        return $this->render('backend/monitoring.html.twig',[
            'logs' => $this->userActivityLogger->getSortedLogEntriesForView()
        ]);
    }
}