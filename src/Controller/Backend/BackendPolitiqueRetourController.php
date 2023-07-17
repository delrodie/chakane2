<?php

namespace App\Controller\Backend;

use App\Entity\PolitiqueRetour;
use App\Form\PolitiqueRetourType;
use App\Repository\PolitiqueRetourRepository;
use App\Service\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/politique-retour')]
class BackendPolitiqueRetourController extends AbstractController
{
    public function __construct(private Utility $utility)
    {
    }

    #[Route('/', name: 'app_backend_politique_retour_index', methods: ['GET'])]
    public function index(PolitiqueRetourRepository $politiqueRetourRepository): Response
    {
        $politique = $politiqueRetourRepository->findOneBy([],['id'=>"DESC"]);
        if ($politique){
            return $this->redirectToRoute('app_backend_politique_retour_show',[
                'id' => $politique->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_backend_politique_retour_new',[], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new', name: 'app_backend_politique_retour_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $politiqueRetour = new PolitiqueRetour();
        $form = $this->createForm(PolitiqueRetourType::class, $politiqueRetour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $politiqueRetour->setSlug($this->utility->slug($politiqueRetour->getTitre()));
            $entityManager->persist($politiqueRetour);
            $entityManager->flush();

            $this->utility->notification("{$politiqueRetour->getTitre()} a été enregistrée avec succès!");

            return $this->redirectToRoute('app_backend_politique_retour_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_politique_retour/new.html.twig', [
            'politique_retour' => $politiqueRetour,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_politique_retour_show', methods: ['GET'])]
    public function show(PolitiqueRetour $politiqueRetour): Response
    {
        return $this->render('backend_politique_retour/show.html.twig', [
            'politique_retour' => $politiqueRetour,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_politique_retour_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PolitiqueRetour $politiqueRetour, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PolitiqueRetourType::class, $politiqueRetour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $politiqueRetour->setSlug($this->utility->slug($politiqueRetour->getTitre()));
            $entityManager->flush();

            $this->utility->notification("{$politiqueRetour->getTitre()} a été modifiée avec succès!");

            return $this->redirectToRoute('app_backend_politique_retour_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_politique_retour/edit.html.twig', [
            'politique_retour' => $politiqueRetour,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_politique_retour_delete', methods: ['POST'])]
    public function delete(Request $request, PolitiqueRetour $politiqueRetour, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$politiqueRetour->getId(), $request->request->get('_token'))) {
            $entityManager->remove($politiqueRetour);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_backend_politique_retour_index', [], Response::HTTP_SEE_OTHER);
    }
}
