<?php

namespace App\Controller\Api;

use App\Service\UserActivityLogger;
use App\Service\Utility;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/monitoring')]
class ApiUserActivityController extends AbstractController
{
    public function __construct(
        private Utility $utility,
        private UserActivityLogger $userActivityLogger
    )
    {
    }

    #[Route('/', name: 'api_useractivity_map', methods: ['GET'])]
    public function map(): JsonResponse
    {
        return new JsonResponse($this->userActivityLogger->getUniqueIPs(), Response::HTTP_OK);
    }

    #[Route('/logs', name: 'api_useractivity_logs', methods: ['GET'])]
    public function logs(): JsonResponse
    {
        return new JsonResponse($this->userActivityLogger->getSortedLogEntriesForView(), Response::HTTP_OK);
    }
}