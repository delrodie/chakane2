<?php

namespace App\Controller\Backend;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\AllRepository;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use App\Service\GestionMedia;
use App\Service\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/produit')]
class BackendProduitController extends AbstractController
{
    public function __construct(
        private AllRepository $allRepository,
        private Utility $utility,
        private GestionMedia $gestionMedia
    )
    {
    }

    #[Route('/', name: 'app_backend_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('backend_produit/index.html.twig', [
            'produits' => $this->allRepository->allCache('produitsIdDesc', true),
            'categories' => $this->allRepository->allCache('categories', true)
        ]);
    }

    #[Route('/new', name: 'app_backend_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$this->utility->codeProduit($produit)) {
                $this->utility->notification("Attention, un produit de même nom existe déjà dans la base de données!", "Error");

                return $this->render('backend_produit/new.html.twig',[
                    'produit' => $produit,
                    'form' => $form
                ]);
            }

            $this->gestionMedia->media($form, $produit, 'produit');
            //$produit->setSlug($this->utility->slug($produit->getTitre()));
            $entityManager->persist($produit);
            $entityManager->flush();

            $this->utility->notification("Le produit {$produit->getTitre()} a été ajouté avec succès!");

            return $this->redirectToRoute('app_backend_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('backend_produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->gestionMedia->media($form, $produit, 'produit');
            $this->utility->codeProduit($produit);
            $entityManager->persist($produit);
            $entityManager->flush();

            $this->utility->notification("Le produit {$produit->getTitre()} a été modifié avec succès!");

            return $this->redirectToRoute('app_backend_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_backend_produit_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/liste/tableaux/t', name: 'app_backend_produit_liste')]
    public function liste()
    {
        return $this->render('backend_produit/liste.html.twig',[
            'produits' => $this->allRepository->allCache('produitsIdDesc', true)
        ]);
    }
}
