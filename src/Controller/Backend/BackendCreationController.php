<?php

namespace App\Controller\Backend;

use App\Entity\Creation;
use App\Entity\ProduitImage;
use App\Form\CreationType;
use App\Form\CreationUpdateType;
use App\Repository\AllRepository;
use App\Repository\CreationRepository;
use App\Service\GestionMedia;
use App\Service\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[Route('/backend/creation')]
class BackendCreationController extends AbstractController
{

    public function __construct(
        private GestionMedia $gestionMedia,
        private AllRepository $allRepository
    )
    {
    }

    #[Route('/', name: 'app_backend_creation_index', methods: ['GET'])]
    public function index(Request $request, CreationRepository $creationRepository, PaginatorInterface $paginator): Response
    {
        $this->allRepository->allCache('creations', true);
        return $this->render('backend_creation/index.html.twig', [
            'creations' => $this->allRepository->creationByPagination(8),
        ]);
    }

    #[Route('/new', name: 'app_backend_creation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $creation = new Creation();
        $form = $this->createForm(CreationType::class, $creation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $medias = $form->get('media')->getData();

            foreach ($medias as $mediaFile){
                $creation = new Creation();
                $media = $this->gestionMedia->upload($mediaFile, 'creation');
                $creation->setSlug($this->slug($mediaFile));
                $creation->setMedia($media);
                $creation->setStatut(true);
                $entityManager->persist($creation);
            }
            //$entityManager->persist($creation);
            $entityManager->flush();

            sweetalert()->addSuccess("Les créations ont été ajoutées avec succès!", 'Enregistrement');

            return $this->redirectToRoute('app_backend_creation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_creation/new.html.twig', [
            'creation' => $creation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_creation_show', methods: ['GET'])]
    public function show(Creation $creation): Response
    {
        return $this->render('backend_creation/show.html.twig', [
            'creation' => $creation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_creation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Creation $creation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CreationUpdateType::class, $creation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $media = $form->get('media')->getData();
            if ($media)$creation->setSlug($this->slug($form->get('media')->getData()));

            $this->gestionMedia->media($form, $creation, 'creation');

            $entityManager->flush();

            return $this->redirectToRoute('app_backend_creation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend_creation/edit.html.twig', [
            'creation' => $creation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_creation_delete', methods: ['POST'])]
    public function delete(Request $request, Creation $creation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$creation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($creation);
            $entityManager->flush();

            if ($creation->getMedia()){
                $this->gestionMedia->removeUpload($creation->getMedia(), 'creation');
            }

            sweetalert()->addSuccess("La création a été supprimée avec succès!");
        }

        return $this->redirectToRoute('app_backend_creation_index', [], Response::HTTP_SEE_OTHER);
    }

    private function slug(UploadedFile $file): string
    {
        // Initialisation du slug
        $slugify = new AsciiSlugger();

        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        return $slugify->slug(strtolower($originalFileName));
    }
}
