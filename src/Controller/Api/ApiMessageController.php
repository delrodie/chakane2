<?php

namespace App\Controller\Api;

use App\Entity\MessageInternaute;
use App\Service\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/messages')]
class ApiMessageController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Utility $utility
    )
    {
    }

    #[Route('/{id}', name: 'app_api_message_put', methods: ['PUT'])]
    public function mise_a_jour(MessageInternaute $message): JsonResponse
    {
        if (!$message) {
            return new JsonResponse(['error' => "Message non trouvé"], Response::HTTP_NOT_FOUND);
        }

        if ($this->isGranted('ROLE_SUPER_ADMIN')) { dump('admin');
            return new JsonResponse(['error' => "Administrateur", Response::HTTP_NOT_MODIFIED]);
        }

        $message->setLecture(true);
        $message->setLectureAt($this->utility->fuseauGMT());

        $this->entityManager->flush();

        return new JsonResponse(['message'=> "Message a été lu!"]);
    }
}