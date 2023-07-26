<?php

namespace App\Controller\Backend;

use App\Entity\Collection;
use App\Form\CollectionType;
use App\Repository\AllRepository;
use App\Repository\CollectionRepository;
use App\Service\GestionMedia;
use App\Service\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/collection')]
class BackendCollectionController extends AbstractController
{
    public function __construct(
        private AllRepository $allRepository,
        private Utility $utility,
        private GestionMedia $gestionMedia
    )
    {
    }

    #[Route('/', name: 'app_backend_collection_index', methods: ['GET'])]
    public function index(CollectionRepository $collectionRepository): Response
    {
        return $this->render('backend_collection/index.html.twig', [
            'collections' => $collectionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_backend_collection_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $collection = new Collection();
        $form = $this->createForm(CollectionType::class, $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->gestionMedia->media($form, $collection, 'collection');
            $collection->setSlug($this->utility->slug($collection->getTitre()));
            $collection->setUpdatedAt($this->utility->fuseauGMT());

            $entityManager->persist($collection);
            $entityManager->flush();

            $this->utility->notification("La collection {$collection->getTitre()} a été ajoutée avec succès!");

            return $this->redirectToRoute('app_backend_collection_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_collection/new.html.twig', [
            'collection' => $collection,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_collection_show', methods: ['GET'])]
    public function show(Collection $collection): Response
    {
        return $this->render('backend_collection/show.html.twig', [
            'collection' => $collection,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_collection_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Collection $collection, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CollectionType::class, $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $collection->setSlug($this->utility->slug($collection->getTitre()));
            $this->gestionMedia->media($form, $collection, 'collection');
            $collection->setUpdatedAt($this->utility->fuseauGMT());

            $entityManager->flush();

            $this->utility->notification("La collection {$collection->getTitre()} a été modifiée");

            return $this->redirectToRoute('app_backend_collection_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_collection/edit.html.twig', [
            'collection' => $collection,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_collection_delete', methods: ['POST'])]
    public function delete(Request $request, Collection $collection, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$collection->getId(), $request->request->get('_token'))) {
            $entityManager->remove($collection);
            $entityManager->flush();

            if ($collection->getMedia()) {
                $this->gestionMedia->removeUpload($collection->getMedia(), 'collection');
            }

            $this->utility->notification("La collection {$collection->getTitre()} a été supprimé vaec succès!");
        }

        return $this->redirectToRoute('app_backend_collection_index', [], Response::HTTP_SEE_OTHER);
    }


}
