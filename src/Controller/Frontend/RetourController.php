<?php

namespace App\Controller\Frontend;

use App\Entity\PolitiqueRetour;
use App\Repository\ConditionRetourRepository;
use App\Repository\PolitiqueRetourRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/politique-condition-retour')]
class RetourController extends AbstractController
{
    #[Route('/', name: 'app_frontend_retour_index')]
    public function index(PolitiqueRetourRepository $politiqueRetourRepository, ConditionRetourRepository $conditionRetourRepository)
    {
        return $this->render('frontend/retour.html.twig',[
            'politique' => $politiqueRetourRepository->findOneBy(['statut' => true],['id'=>'DESC']),
            'condition' => $conditionRetourRepository->findOneBy(['statut' => true],['id' => 'DESC'])
        ]);
    }
}