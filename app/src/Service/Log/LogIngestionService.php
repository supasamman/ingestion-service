<?php

declare(strict_types=1);

namespace App\Service\Log;

use App\Contract\LogIngestionServiceInterface;
use App\DTO\LogEntryDTO;
use App\Exception\MessageBusException;
use App\Message\LogBatchMessage;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

final readonly class LogIngestionService implements LogIngestionServiceInterface
{
    public function __construct(
        #[Autowire('%log_batch_size%')]
        private int $batchSize,
        private MessageBusInterface $bus,
        private LogPriorityService $priorityService,
        private LogValidatorService $validator,
    ) {}

    /**
     * @param array<LogEntryDTO> $logs
     */
    public function ingest(array $logs): string
    {
        $validated = $this->validator->validate(logs: $logs);

        $batchId = 'batch_' . Uuid::v7()->toRfc4122();

        foreach (array_chunk(array: $validated, length: $this->batchSize) as $chunk) {
            $priority = $this->priorityService->getChunkPriority(dtos: $chunk);

            $message = new LogBatchMessage(
                batchId: $batchId,
                logs: $chunk,
                publishedAt: new \DateTimeImmutable(),
            );

            $stamps = [
                new AmqpStamp(flags: AMQP_NOPARAM, attributes: ['priority' => $priority]),
            ];

            try {
                $this->bus->dispatch($message, $stamps);
            } catch (ExceptionInterface $e) {
                throw new MessageBusException(batchId: $batchId, previous: $e);
            }
        }

        return $batchId;
    }
}
