<?php

declare(strict_types=1);

namespace App\Controller;

use App\Contract\LogIngestionServiceInterface;
use App\Enum\ResponseStatus;
use App\Request\IngestLogsRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class LogIngestionController extends AbstractController
{
    public function __construct(
        private readonly LogIngestionServiceInterface $ingestion,
    ) {}

    #[Route('api/logs/ingest', name: 'app_log_ingestion', methods: ['POST'])]
    public function ingest(#[MapRequestPayload(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] IngestLogsRequest $request): JsonResponse
    {
        $batchId = $this->ingestion->ingest($request->logs);

        return $this->json(
            data: [
                'status' => ResponseStatus::ACCEPTED->value,
                'batchId' => $batchId,
                'logs_count' => \count(value: $request->logs),
            ],
            status: Response::HTTP_ACCEPTED,
        );
    }
}
