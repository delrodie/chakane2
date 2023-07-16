<?php

namespace App\Controller\Backend;

use App\Entity\Confidentialite;
use App\Form\ConfidentialiteType;
use App\Repository\ConfidentialiteRepository;
use App\Service\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/confidentialite')]
class BackendConfidentialiteController extends AbstractController
{
    public function __construct(private Utility $utility)
    {
    }

    #[Route('/', name: 'app_backend_confidentialite_index', methods: ['GET'])]
    public function index(ConfidentialiteRepository $confidentialiteRepository): Response
    {
        $confidentialite = $confidentialiteRepository->findOneBy([],['id'=>'DESC']);
        if ($confidentialite) {
            return $this->redirectToRoute('app_backend_confidentialite_show', [
                'id' => $confidentialite->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_backend_confidentialite_new',[], Response::HTTP_SEE_OTHER);;
    }

    #[Route('/new', name: 'app_backend_confidentialite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $confidentialite = new Confidentialite();
        $form = $this->createForm(ConfidentialiteType::class, $confidentialite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $confidentialite->setSlug($this->utility->slug($confidentialite->getTitre()));
            $entityManager->persist($confidentialite);
            $entityManager->flush();

            $this->utility->notification("{$confidentialite->getTitre()} a été enregistré(e) avec succès!");

            return $this->redirectToRoute('app_backend_confidentialite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_confidentialite/new.html.twig', [
            'confidentialite' => $confidentialite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_confidentialite_show', methods: ['GET'])]
    public function show(Confidentialite $confidentialite): Response
    {
        return $this->render('backend_confidentialite/show.html.twig', [
            'confidentialite' => $confidentialite,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_confidentialite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Confidentialite $confidentialite, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ConfidentialiteType::class, $confidentialite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->utility->notification("{$confidentialite->getTitre()} a été modifiée avec succès!");

            return $this->redirectToRoute('app_backend_confidentialite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_confidentialite/edit.html.twig', [
            'confidentialite' => $confidentialite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_confidentialite_delete', methods: ['POST'])]
    public function delete(Request $request, Confidentialite $confidentialite, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$confidentialite->getId(), $request->request->get('_token'))) {
            $entityManager->remove($confidentialite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_backend_confidentialite_index', [], Response::HTTP_SEE_OTHER);
    }
}
