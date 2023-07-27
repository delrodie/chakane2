<?php

namespace App\Controller\Backend;

use App\Entity\MessageInternaute;
use App\Form\MessageInternauteType;
use App\Repository\MessageInternauteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/message/internaute')]
class BackendMessageInternauteController extends AbstractController
{
    #[Route('/', name: 'app_backend_message_internaute_index', methods: ['GET'])]
    public function index(MessageInternauteRepository $messageInternauteRepository): Response
    {
        return $this->render('backend_message_internaute/index.html.twig', [
            'message_internautes' => $messageInternauteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_backend_message_internaute_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $messageInternaute = new MessageInternaute();
        $form = $this->createForm(MessageInternauteType::class, $messageInternaute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($messageInternaute);
            $entityManager->flush();

            return $this->redirectToRoute('app_backend_message_internaute_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_message_internaute/new.html.twig', [
            'message_internaute' => $messageInternaute,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_message_internaute_show', methods: ['GET'])]
    public function show(MessageInternaute $messageInternaute): Response
    {
        return $this->render('backend_message_internaute/show.html.twig', [
            'message_internaute' => $messageInternaute,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_message_internaute_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MessageInternaute $messageInternaute, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MessageInternauteType::class, $messageInternaute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_backend_message_internaute_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_message_internaute/edit.html.twig', [
            'message_internaute' => $messageInternaute,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_message_internaute_delete', methods: ['POST'])]
    public function delete(Request $request, MessageInternaute $messageInternaute, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$messageInternaute->getId(), $request->request->get('_token'))) {
            $entityManager->remove($messageInternaute);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_backend_message_internaute_index', [], Response::HTTP_SEE_OTHER);
    }
}
