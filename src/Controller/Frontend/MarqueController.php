<?php

namespace App\Controller\Frontend;

use App\Repository\MarqueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/la-marque')]
class MarqueController extends AbstractController
{
    #[Route('/', name: "app_frontend_marque_index")]
    public function index(MarqueRepository $marqueRepository): Response
    {
        return $this->render('frontend/marque.html.twig',[
            'marque' => $marqueRepository->findOneBy([],['id'=>"DESC"])
        ]);
    }
}