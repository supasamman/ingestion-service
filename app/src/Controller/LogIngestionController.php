<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Enum\ResponseStatus;
use App\Service\Log\LogIngestionService;
use App\Service\Log\LogValidatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class LogIngestionController extends AbstractController
{
    public function __construct(
        #[Autowire('%log_batch_max_size%')]
        private readonly int $maxBatchSize,
        private readonly LogValidatorService $validator,
        private readonly LogIngestionService $ingestion,
    )
    {}

    #[Route('api/logs/ingest', name: 'app_log_ingestion', methods: ['POST'])]
    public function ingest(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $logs = $data['logs'];

        if (count($logs) > $this->maxBatchSize) {
            return $this->json(
                [
                    'status' => ResponseStatus::ERROR->value,
                    'errors' => ["Maximum {$this->maxBatchSize} logs per batch"],
                ],
                Response::HTTP_BAD_REQUEST);
        }

        [$validated, $errors] = $this->validator->validate($logs);

        if (!empty($errors)) {
            return $this->json(
                [
                    'status' => ResponseStatus::ERROR->value,
                    'errors' => $errors
                ],
                Response::HTTP_BAD_REQUEST);
        }

        try {
            $batchId = $this->ingestion->ingest($validated);

            return $this->json(
                [
                    'status' => ResponseStatus::ACCEPTED->value,
                    'batchId' => $batchId,
                    'logs_count' => count($validated),

                ],
                RESPONSE::HTTP_ACCEPTED
            );

        } catch (ExceptionInterface $e) {
            return $this->json(
                [
                    'status' => ResponseStatus::ERROR->value,
                    'message' => 'Service unavailable'
                ],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }
}
