<?php

declare(strict_types=1);

namespace App\Controller;

use App\Contract\LogIngestionServiceInterface;
use App\Enum\ResponseStatus;
use App\Request\IngestLogsRequest;
use App\Service\Log\LogValidatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class LogIngestionController extends AbstractController
{
    public function __construct(
        private readonly LogValidatorService $validator,
        private readonly LogIngestionServiceInterface $ingestion,
    ) {
    }

    #[Route('api/logs/ingest', name: 'app_log_ingestion', methods: ['POST'])]
    public function ingest(#[MapRequestPayload(validationFailedStatusCode: 400)] IngestLogsRequest $request): JsonResponse
    {
        [$validated, $errors] = $this->validator->validate($request->logs);

        if (!empty($errors)) {
            return $this->json(
                [
                    'status' => ResponseStatus::ERROR->value,
                    'errors' => $errors,
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $batchId = $this->ingestion->ingest($validated);

            return $this->json(
                [
                    'status' => ResponseStatus::ACCEPTED->value,
                    'batchId' => $batchId,
                    'logs_count' => count($validated),
                ],
                Response::HTTP_ACCEPTED
            );
        } catch (ExceptionInterface $e) {
            return $this->json(
                [
                    'status' => ResponseStatus::ERROR->value,
                    'message' => 'Service unavailable',
                ],
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }
    }
}
