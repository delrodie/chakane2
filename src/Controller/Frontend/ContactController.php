<?php

namespace App\Controller\Frontend;

use App\Repository\AllRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/contact')]
class ContactController extends AbstractController
{
    public function __construct(private AllRepository $allRepository)
    {
    }

    #[Route('/', name: 'app_frontend_contact_index')]
    public function index(): Response
    {

        return $this->render('frontend/contact.html.twig',[
            'contact' => $this->allRepository->allCache('contact')
        ]);
    }

    #[Route('/header', name: 'app_frontend_contact_header')]
    public function header()
    {
        return $this->render('frontend/contact_header.html.twig',[
            'contact' => $this->allRepository->allCache('contact')
        ]);
    }
}