<?php

declare(strict_types = 1);

namespace App\DTO;

use App\Enum\LogLevel;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class LogEntryDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\DateTime(DateTimeInterface::ATOM)]
        public string $timestamp,

        #[Assert\NotBlank]
        public LogLevel $level,

        #[Assert\NotBlank]
        public string $service,

        #[Assert\NotBlank]
        public string $message,

        public ?array $context = null,
        public ?string $trace_id = null,
    ) {}
}
