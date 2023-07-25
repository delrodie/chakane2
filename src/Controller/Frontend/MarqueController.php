<?php

namespace App\Controller\Frontend;

use App\Repository\AllRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/la-marque')]
class MarqueController extends AbstractController
{
    #[Route('/', name: "app_frontend_marque_index")]
    public function index(AllRepository $allRepository): Response
    {
        return $this->render('frontend/marque.html.twig',[
            'marque' => $allRepository->allCache('marque')
        ]);
    }
}