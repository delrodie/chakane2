<?php

namespace App\Controller\Backend;

use App\Repository\AllRepository;
use App\Service\Utility;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/devise')]
class BackendDeviseController extends AbstractController
{
    #[Route('/', name: 'app_backend_devise_index')]
    public function index(AllRepository $allRepository): Response
    {
        return $this->render('backend/devise.html.twig',[
            'devise' => $allRepository->allCache('devise', true)
        ]);
    }

    #[ROute('/update', name: 'app_backend_devise_update')]
    public function update(Utility $utility): Response
    {
        $utility->saveDevise();

        $utility->notification("La mise à jour des devises a été effectuée avec succès!");

        return $this->redirectToRoute('app_backend_devise_index',[], Response::HTTP_SEE_OTHER);
    }
}