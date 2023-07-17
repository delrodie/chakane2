<?php

namespace App\Controller\Backend;

use App\Entity\MentionLegale;
use App\Form\MentionLegaleType;
use App\Repository\MentionLegaleRepository;
use App\Service\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/mention-legale')]
class BackendMentionLegaleController extends AbstractController
{
    public function __construct(private Utility $utility)
    {
    }

    #[Route('/', name: 'app_backend_mention_legale_index', methods: ['GET'])]
    public function index(MentionLegaleRepository $mentionLegaleRepository): Response
    {
        $mention = $mentionLegaleRepository->findOneBy([],['id'=>"DESC"]);
        if ($mention){
            return $this->redirectToRoute('app_backend_mention_legale_show',[
                'id' => $mention->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute("app_backend_mention_legale_new",[], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new', name: 'app_backend_mention_legale_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $mentionLegale = new MentionLegale();
        $form = $this->createForm(MentionLegaleType::class, $mentionLegale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mentionLegale->setSlug($this->utility->slug($mentionLegale->getTitre()));
            $entityManager->persist($mentionLegale);
            $entityManager->flush();

            $this->utility->notification("{$mentionLegale->getTitre()} a été ajoutée avec succès!");

            return $this->redirectToRoute('app_backend_mention_legale_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_mention_legale/new.html.twig', [
            'mention_legale' => $mentionLegale,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_mention_legale_show', methods: ['GET'])]
    public function show(MentionLegale $mentionLegale): Response
    {
        return $this->render('backend_mention_legale/show.html.twig', [
            'mention_legale' => $mentionLegale,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_mention_legale_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MentionLegale $mentionLegale, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MentionLegaleType::class, $mentionLegale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mentionLegale->setSlug($this->utility->slug($mentionLegale->getTitre()));
            $entityManager->flush();

            $this->utility->notification("{$mentionLegale->getTitre()} a été modifiée avec succès!");

            return $this->redirectToRoute('app_backend_mention_legale_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_mention_legale/edit.html.twig', [
            'mention_legale' => $mentionLegale,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_mention_legale_delete', methods: ['POST'])]
    public function delete(Request $request, MentionLegale $mentionLegale, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mentionLegale->getId(), $request->request->get('_token'))) {
            $entityManager->remove($mentionLegale);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_backend_mention_legale_index', [], Response::HTTP_SEE_OTHER);
    }
}
