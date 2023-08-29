<?php

namespace App\Controller\Frontend;

use App\Repository\AllRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/nos-creations')]
class CreationController extends AbstractController
{
    public function __construct(private AllRepository $allRepository)
    {
    }

    #[Route('/', name:"app_frontend_creation_index")]
    public function index()
    {
        return $this->render('frontend/creations.html.twig',[
            'creations' => $this->allRepository->creationByPagination(12)
        ]);
    }
}