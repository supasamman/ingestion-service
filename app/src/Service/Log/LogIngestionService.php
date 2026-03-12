<?php

declare(strict_types = 1);

namespace App\Service\Log;

use App\Contract\LogIngestionServiceInterface;
use App\Message\LogBatchMessage;
use DateTimeImmutable;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

final readonly class LogIngestionService implements LogIngestionServiceInterface
{
    public function __construct(
        private MessageBusInterface $bus,
        private LogPriorityService $priorityService,
    )
    {}

    /*
     * @param array<LogEntryDTO> $logs
    */
    /**
     * @throws ExceptionInterface
     */
    public function ingest(array $logs): string
    {
        $batchId = 'batch_' . Uuid::v7()->toRfc4122();

        $priority = $this->priorityService->getBatchPriority($logs);

        $this->bus->dispatch(
            new LogBatchMessage
            (
                batchId: $batchId,
                logs: $logs,
                publishedAt: new DateTimeImmutable(),
            ),
            [new AmqpStamp(null, AMQP_NOPARAM, ['priority' => $priority])]
        );

        return $batchId;
    }
}
