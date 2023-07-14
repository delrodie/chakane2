<?php

namespace App\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_backend_dashboard_index')]
    public function index()
    {
        return $this->render('backend/dashboard.html.twig');
    }
}