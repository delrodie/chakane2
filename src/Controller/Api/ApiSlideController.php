<?php

namespace App\Controller\Api;

use App\Repository\AllRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/slides')]
class ApiSlideController extends AbstractController
{
    public function __construct(private AllRepository $allRepository)
    {
    }

    #[Route('/', name: 'app_api_slide_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $slides = $this->allRepository->allCache('slides');

        return new JsonResponse($slides, Response::HTTP_OK);
    }
}