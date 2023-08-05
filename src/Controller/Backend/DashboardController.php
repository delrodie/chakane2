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
        // https://ipinfo.io/developers
        // https://ipinfo.io/account/home
        // https://developer.fedex.com/api/fr-fr/catalog/rate/v1/docs.html
        // https://developer.fedex.com/api/fr-fr/catalog/authorization/v1/docs.html
        return $this->render('backend/dashboard.html.twig',[
            'devise' => $this->allRepository->allCache('devise', true)
        ])->setSharedMaxAge(3600);
    }


}