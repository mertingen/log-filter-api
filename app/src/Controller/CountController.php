<?php

namespace App\Controller;

use App\Service\LogService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CountController extends AbstractController
{
    #[Route('/count', name: 'count', methods: ['GET'])]
    public function countRows(Request $request, LogService $logService): JsonResponse
    {
        try {
            // Get the filter criteria from the request's query parameters
            $data = [
                'serviceNames' => $request->query->all()['serviceNames'] ?? [],
                'statusCode' => $request->query->get('statusCode'),
                'startDate' => $request->query->get('startDate'),
                'endDate' => $request->query->get('endDate'),
            ];

            // Get the count of rows that match the filter criteria using the LogService
            $count = $logService->getCount($data);

            // Return the count as a JSON response
            return $this->json(['counter' => $count]);
        } catch (\Exception $e) {
            // Handle any exceptions that may occur during the process and return an error response
            return $this->json(['error' => $e->getMessage()]);
        }
    }
}