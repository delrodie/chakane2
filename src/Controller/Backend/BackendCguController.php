<?php

namespace App\Controller\Backend;

use App\Entity\Cgu;
use App\Form\CguType;
use App\Repository\CguRepository;
use App\Service\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/cgu')]
class BackendCguController extends AbstractController
{
    public function __construct(
        private Utility $utility
    )
    {
    }

    #[Route('/', name: 'app_backend_cgu_index', methods: ['GET'])]
    public function index(CguRepository $cguRepository): Response
    {
        $cgu = $cguRepository->findOneBy([],['id'=>"desc"]);
        if ($cgu)
            return $this->redirectToRoute('app_backend_cgu_show',['id'=>$cgu->getId()], Response::HTTP_SEE_OTHER);

        return $this->redirectToRoute('app_backend_cgu_new',[],Response::HTTP_SEE_OTHER);
    }

    #[Route('/new', name: 'app_backend_cgu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cgu = new Cgu();
        $form = $this->createForm(CguType::class, $cgu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cgu->setSlug($this->utility->slug($cgu->getTitre()));
            $entityManager->persist($cgu);
            $entityManager->flush();

            return $this->redirectToRoute('app_backend_cgu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_cgu/new.html.twig', [
            'cgu' => $cgu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_cgu_show', methods: ['GET'])]
    public function show(Cgu $cgu): Response
    {
        return $this->render('backend_cgu/show.html.twig', [
            'cgu' => $cgu,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_cgu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cgu $cgu, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CguType::class, $cgu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cgu->setSlug($this->utility->slug($cgu->getTitre()));
            $entityManager->flush();

            return $this->redirectToRoute('app_backend_cgu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_cgu/edit.html.twig', [
            'cgu' => $cgu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_cgu_delete', methods: ['POST'])]
    public function delete(Request $request, Cgu $cgu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cgu->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cgu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_backend_cgu_index', [], Response::HTTP_SEE_OTHER);
    }
}
