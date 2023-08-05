<?php

namespace App\Controller\Backend;

use App\Entity\Produit;
use App\Entity\ProduitImage;
use App\Form\ProduitImageType;
use App\Repository\ProduitImageRepository;
use App\Service\GestionMedia;
use App\Service\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/produit/image')]
class BackendProduitImageController extends AbstractController
{
    public function __construct(private GestionMedia $gestionMedia, private Utility $utility)
    {
    }

    #[Route('/', name: 'app_backend_produit_image_index', methods: ['GET'])]
    public function index(ProduitImageRepository $produitImageRepository): Response
    {
        return $this->render('backend_produit_image/index.html.twig', [
            'produit_images' => $produitImageRepository->findAll(),
        ]);
    }

    #[Route('/new/{produit}', name: 'app_backend_produit_image_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Produit $produit): Response
    {
        $produitImage = new ProduitImage();
        $form = $this->createForm(ProduitImageType::class, $produitImage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $medias = $form->get('media')->getData();

            foreach ($medias as $mediaFile){
                $produitImage = new ProduitImage();
                $media = $this->gestionMedia->upload($mediaFile, 'produit');
                $produitImage->setMedia($media);
                $produitImage->setProduit($produit);
                $entityManager->persist($produitImage);
            }
            $entityManager->flush();

            $this->utility->notification("Les photos du produit {$produit->getTitre()} ont ete ajoutées avec succès!");

            return $this->redirectToRoute('app_backend_produit_show', ['id' => $produit->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_produit_image/new.html.twig', [
            'produit_image' => $produitImage,
            'form' => $form,
            'produit' => $produit
        ]);
    }

    #[Route('/{id}', name: 'app_backend_produit_image_show', methods: ['GET'])]
    public function show(ProduitImage $produitImage): Response
    {
        return $this->render('backend_produit_image/show.html.twig', [
            'produit_image' => $produitImage,
        ]);
    }

    #[Route('/{id}/edit/{produit}', name: 'app_backend_produit_image_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProduitImage $produitImage, EntityManagerInterface $entityManager, Produit $produit): Response
    {
        $form = $this->createForm(ProduitImageType::class, $produitImage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->gestionMedia->media($form, $produitImage, 'produit');
            $entityManager->flush();

            $this->utility->notification("L'image a été supprimée avec succès!");

            return $this->redirectToRoute('app_backend_produit_image_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_produit_image/edit.html.twig', [
            'produit_image' => $produitImage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/{produit}', name: 'app_backend_produit_image_delete', methods: ['POST'])]
    public function delete(Request $request, ProduitImage $produitImage, EntityManagerInterface $entityManager, Produit $produit): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produitImage->getId(), $request->request->get('_token'))) {
            $entityManager->remove($produitImage);
            $entityManager->flush();

            if ($produitImage->getMedia())
                $this->gestionMedia->removeUpload($produitImage->getMedia(), 'produit');

            $this->utility->notification("L'image a été supprimée avec succès!");
        }

        return $this->redirectToRoute('app_backend_produit_show', ['id' => $produit->getId()], Response::HTTP_SEE_OTHER);
    }
}
