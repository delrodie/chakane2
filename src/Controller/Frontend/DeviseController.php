<?php

namespace App\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/devise')]
class DeviseController extends AbstractController
{
    #[Route('/devise', name: 'app_frontend_devise_index')]
    public function index(Request $request)
    {
        // Récupération de la dévise selectionnée
        $devise = $request->request->get('_devise');

        // Mise en session de la devise
        $session = $request->getSession();
        $session->set('devise', $devise);

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}