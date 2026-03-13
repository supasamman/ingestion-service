<?php

declare(strict_types=1);

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class IngestLogsRequest
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Count(
            min: 1,
            max: 1_000,
            minMessage: 'You must provide at least one log entry.',
            maxMessage: 'You cannot provide more than 1000 log entries.',
        )]
        public array $logs,
    ) {}
}
