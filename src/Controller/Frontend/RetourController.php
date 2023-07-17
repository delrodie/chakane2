<?php

namespace App\Controller\Frontend;

use App\Entity\PolitiqueRetour;
use App\Repository\PolitiqueRetourRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/politique-condition-retour')]
class RetourController extends AbstractController
{
    #[Route('/', name: 'app_frontend_retour_index')]
    public function index(PolitiqueRetourRepository $politiqueRetourRepository)
    {
        return $this->render('frontend/retour.html.twig',[
            'politique' => $politiqueRetourRepository->findOneBy(['statut' => true],['id'=>'DESC']),
            'condition' => null
        ]);
    }
}