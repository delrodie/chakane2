<?php

namespace App\Controller\Backend;

use App\Entity\Marque;
use App\Form\MarqueType;
use App\Repository\MarqueRepository;
use App\Service\GestionMedia;
use App\Service\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/marque')]
class BackendMarqueController extends AbstractController
{
    public function __construct(
        private GestionMedia $gestionMedia, private Utility $utility
    )
    {
    }

    #[Route('/', name: 'app_backend_marque_index', methods: ['GET'])]
    public function index(MarqueRepository $marqueRepository): Response
    {
        $marque = $marqueRepository->findOneBy([],['id'=>'DESC']);
        if ($marque){
            return $this->redirectToRoute('app_backend_marque_edit',[
                'id' => $marque->getId()
            ], Response::HTTP_SEE_OTHER);
        }
        return $this->redirectToRoute('app_backend_marque_new',[], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new', name: 'app_backend_marque_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $marque = new Marque();
        $form = $this->createForm(MarqueType::class, $marque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->media($form, $marque);

            $entityManager->persist($marque);
            $entityManager->flush();

            $this->utility->notification("La marque {$marque->getTitre()} a été ajoutée avec succès!");

            return $this->redirectToRoute('app_backend_marque_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_marque/new.html.twig', [
            'marque' => $marque,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_marque_show', methods: ['GET'])]
    public function show(Marque $marque): Response
    {
        return $this->render('backend_marque/show.html.twig', [
            'marque' => $marque,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_marque_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Marque $marque, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MarqueType::class, $marque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->media($form, $marque);

            $entityManager->flush();

            $this->utility->notification("La marque '{$marque->getTitre()} a été modifiée avec succès!");

            return $this->redirectToRoute('app_backend_marque_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_marque/edit.html.twig', [
            'marque' => $marque,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_marque_delete', methods: ['POST'])]
    public function delete(Request $request, Marque $marque, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$marque->getId(), $request->request->get('_token'))) {
            $entityManager->remove($marque);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_backend_marque_index', [], Response::HTTP_SEE_OTHER);
    }

    protected function media($form, $marque): void
    {
        // Gestion des médias
        $mediaFile = $form->get('media')->getData();
        if ($mediaFile){
            $media = $this->gestionMedia->upload($mediaFile, 'marque');

            if ($marque->getMedia()){
                $this->gestionMedia->removeUpload($marque->getMedia(), 'marque');
            }

            $marque->setMedia($media);
        }
    }
}
