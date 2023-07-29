<?php

namespace App\Controller\Backend;

use App\Entity\Type;
use App\Form\TypeType;
use App\Repository\TypeRepository;
use App\Service\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/type')]
class BackendTypeController extends AbstractController
{
    public function __construct(
        private Utility $utility
    )
    {
    }

    #[Route('/', name: 'app_backend_type_index', methods: ['GET','POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, TypeRepository $typeRepository): Response
    {
        $type = new Type();
        $form = $this->createForm(TypeType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $type->setSlug($this->utility->slug($type->getTitre()));
            $type->setCode($this->utility->codeType());

            $entityManager->persist($type);
            $entityManager->flush();

            $this->utility->notification("Le type '{$type->getTitre()}' ");

            return $this->redirectToRoute('app_backend_type_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('backend_type/index.html.twig', [
            'types' => $typeRepository->findAll(),
            'type' => $type,
            'form' => $form,
            'suppression' => false
        ]);
    }

    #[Route('/new', name: 'app_backend_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $type = new Type();
        $form = $this->createForm(TypeType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $type->setSlug($this->utility->slug($type->getTitre()));
            $type->setCode($this->utility->codeType());

            $entityManager->persist($type);
            $entityManager->flush();

            $this->utility->notification("Le type '{$type->getTitre()}' ");

            return $this->redirectToRoute('app_backend_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_type/new.html.twig', [
            'type' => $type,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_type_show', methods: ['GET'])]
    public function show(Type $type): Response
    {
        return $this->render('backend_type/show.html.twig', [
            'type' => $type,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Type $type, EntityManagerInterface $entityManager, TypeRepository $typeRepository): Response
    {
        $form = $this->createForm(TypeType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $type->setSlug($this->utility->slug($type->getTitre()));
            $entityManager->flush();

            $this->utility->notification("Le type '{$type->getTitre()}' ");

            return $this->redirectToRoute('app_backend_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_type/edit.html.twig', [
            'types' => $typeRepository->findAll(),
            'type' => $type,
            'form' => $form,
            'suppression' => true
        ]);
    }

    #[Route('/{id}', name: 'app_backend_type_delete', methods: ['POST'])]
    public function delete(Request $request, Type $type, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$type->getId(), $request->request->get('_token'))) {
            $entityManager->remove($type);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_backend_type_index', [], Response::HTTP_SEE_OTHER);
    }
}
