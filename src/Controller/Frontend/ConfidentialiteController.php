<?php

namespace App\Controller\Frontend;

use App\Repository\ConfidentialiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/confidentialites')]
class ConfidentialiteController extends AbstractController
{
    #[Route('/', name: 'app_frontend_confidentialite_index')]
    public function index(ConfidentialiteRepository $confidentialiteRepository)
    {
        return $this->render('frontend/confidentialite.html.twig',[
            'confidentialite' => $confidentialiteRepository->findOneBy(['statut' => true],['id' => 'DESC'])
        ]);
    }
}