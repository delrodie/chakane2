<?php

namespace App\Controller\Backend;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\AllRepository;
use App\Repository\CategorieRepository;
use App\Service\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/categorie')]
class BackendCategorieController extends AbstractController
{
    public function __construct(Private Utility $utility, private AllRepository $allRepository)
    {
    }

    #[Route('/', name: 'app_backend_categorie_index', methods: ['GET','POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, CategorieRepository $categorieRepository): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->codeCategorie($categorie);
            $entityManager->persist($categorie);
            $entityManager->flush();

            $this->utility->notification("La catégorie '{$categorie->getTitre()} a été sauvegardée avec succès!");

            return $this->redirectToRoute('app_backend_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'categorie' => $categorie,
            'form' => $form,
            'suppression' => false
        ]);
    }

    #[Route('/new', name: 'app_backend_categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorie);
            $this->utility->codeCategorie($categorie, true);
            $entityManager->flush();

            $this->utility->notification("La catégorie '{$categorie->getTitre()} a été mise a jour avec succès!");

            return $this->redirectToRoute('app_backend_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_categorie_show', methods: ['GET'])]
    public function show(Categorie $categorie): Response
    {
        return $this->render('backend_categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_categorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categorie $categorie, EntityManagerInterface $entityManager, CategorieRepository $categorieRepository): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->utility->codeCategorie($categorie);
            $entityManager->flush();

            $this->utility->notification("La catégorie '{$categorie->getTitre()} a été mise a jour avec succès!");

            return $this->redirectToRoute('app_backend_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_categorie/edit.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'categorie' => $categorie,
            'form' => $form,
            'suppression' => true
        ]);
    }

    #[Route('/{id}', name: 'app_backend_categorie_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($categorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_backend_categorie_index', [], Response::HTTP_SEE_OTHER);
    }
}
