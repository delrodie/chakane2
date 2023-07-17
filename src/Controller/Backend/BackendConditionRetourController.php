<?php

namespace App\Controller\Backend;

use App\Entity\ConditionRetour;
use App\Form\ConditionRetourType;
use App\Repository\ConditionRetourRepository;
use App\Service\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/condition-retour')]
class BackendConditionRetourController extends AbstractController
{
    public function __construct(private Utility $utility)
    {

    }
    #[Route('/', name: 'app_backend_condition_retour_index', methods: ['GET'])]
    public function index(ConditionRetourRepository $conditionRetourRepository): Response
    {
        $condition = $conditionRetourRepository->findOneBy([],['id'=>"DESC"]);
        if ($condition){
            return $this->redirectToRoute('app_backend_condition_retour_show',[
                'id' => $condition->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_backend_condition_retour_new',[], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new', name: 'app_backend_condition_retour_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $conditionRetour = new ConditionRetour();
        $form = $this->createForm(ConditionRetourType::class, $conditionRetour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conditionRetour->setSlug($this->utility->slug($conditionRetour->getTitre()));
            $entityManager->persist($conditionRetour);
            $entityManager->flush();

            $this->utility->notification("{$conditionRetour->getTitre()} a été enregistrée avec succès!");

            return $this->redirectToRoute('app_backend_condition_retour_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_condition_retour/new.html.twig', [
            'condition_retour' => $conditionRetour,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_condition_retour_show', methods: ['GET'])]
    public function show(ConditionRetour $conditionRetour): Response
    {
        return $this->render('backend_condition_retour/show.html.twig', [
            'condition_retour' => $conditionRetour,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_condition_retour_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ConditionRetour $conditionRetour, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ConditionRetourType::class, $conditionRetour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conditionRetour->setSlug($this->utility->slug($conditionRetour->getTitre()));
            $entityManager->flush();

            $this->utility->notification("{$conditionRetour->getTitre()} a été modifiée avec succès!");

            return $this->redirectToRoute('app_backend_condition_retour_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_condition_retour/edit.html.twig', [
            'condition_retour' => $conditionRetour,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_condition_retour_delete', methods: ['POST'])]
    public function delete(Request $request, ConditionRetour $conditionRetour, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$conditionRetour->getId(), $request->request->get('_token'))) {
            $entityManager->remove($conditionRetour);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_backend_condition_retour_index', [], Response::HTTP_SEE_OTHER);
    }
}
