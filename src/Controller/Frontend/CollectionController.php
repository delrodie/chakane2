<?php

namespace App\Controller\Frontend;

use App\Entity\Collection;
use App\Repository\AllRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/collection')]
class CollectionController extends AbstractController
{
    public function __construct(private AllRepository $allRepository)
    {
    }

    #[Route('/', name: 'app_frontend_collection_index')]
    public function index(): Response
    {
        return $this->render('frontend/collections.html.twig',[
            'collections' => $this->allRepository->allCache('collections')
        ]);
    }

    #[Route('/{slug}', name: 'app_frontend_collection_show', methods: ['GET'])]
    public function show(Collection $collection)
    {
        return $this->render('frontend/collection_show.html.twig',[
            'collection' => $collection
        ]);
    }
}