<?php

namespace App\Controller\Frontend;

use App\Repository\MentionLegaleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mention-legale')]
class MentionLegaleController extends AbstractController
{
    #[Route('/', name: 'app_frontend_mentionlegale_index')]
    public function index(MentionLegaleRepository $mentionLegaleRepository)
    {
        return $this->render('frontent/mention_legale.html.twig',[
            'mention' => $mentionLegaleRepository->findOneBy(['statut'=>true],['id'=>"DESC"])
        ]);
    }
}