<?php

declare(strict_types=1);

namespace App\Message;

final readonly class LogBatchMessage
{
    public function __construct(
        public string $batchId,
        public array $logs,
        public \DateTimeImmutable $publishedAt,
    ) {}
}
