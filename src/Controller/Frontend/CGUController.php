<?php

namespace App\Controller\Frontend;

use App\Repository\CguRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/CGU')]
class CGUController extends AbstractController
{
    #[Route('/', name: 'app_frontend_cgu_index')]
    public function index(CguRepository $cguRepository)
    {
        return $this->render('frontend/cgu.html.twig',[
            'cgu' => $cguRepository->findOneBy(['statut' => true],['id'=>'DESC'])
        ]);
    }
}