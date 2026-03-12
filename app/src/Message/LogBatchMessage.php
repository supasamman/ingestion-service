<?php

declare(strict_types=1);

namespace App\Message;

use DateTimeImmutable;

final readonly class LogBatchMessage
{
    public function __construct(
        public string $batchId,
        public array $logs,
        public DateTimeImmutable $publishedAt,
    ) {
    }
}
