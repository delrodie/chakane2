<?php

namespace App\Controller\Frontend;

use App\Entity\MessageInternaute;
use App\Form\MessageInternauteType;
use App\Repository\AllRepository;
use App\Service\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/contact')]
class ContactController extends AbstractController
{
    public function __construct(
        private AllRepository $allRepository,
        private EntityManagerInterface $entityManager,
        private Utility $utility
    )
    {
    }

    #[Route('/', name: 'app_frontend_contact_index', methods: ['GET','POST'])]
    public function index(Request $request): Response
    {
        $messageInternaute = new MessageInternaute();
        $form = $this->createForm(MessageInternauteType::class, $messageInternaute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $messageInternaute->setCreatedAt($this->utility->fuseauGMT());
            $this->entityManager->persist($messageInternaute);
            $this->entityManager->flush();

            sweetalert()->addSuccess("Votre message a été envoyé avec succès! Nous vous repondrons sous peu");

//            $this->utility->notification();

            return $this->redirectToRoute('app_frontend_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('frontend/contact.html.twig',[
            'contact' => $this->allRepository->allCache('contact'),
            'form' => $form,
        ]);
    }

    #[Route('/header', name: 'app_frontend_contact_header')]
    public function header()
    {
        return $this->render('frontend/contact_header.html.twig',[
            'contact' => $this->allRepository->allCache('contact')
        ]);
    }
}