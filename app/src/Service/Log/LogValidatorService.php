<?php

declare(strict_types = 1);

namespace App\Service\Log;

use App\DTO\LogEntryDTO;
use App\Enum\LogLevel;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class LogValidatorService
{
    public function __construct(
        private ValidatorInterface $validator
    )
    {}

    public function validate(array $logs): array
    {
        $dtos = [];
        $errors = [];

        foreach ($logs as $i => $log) {
            $level = LogLevel::tryFrom($log['level'] ?? '');

            if ($level === null) {
                $errors[] = "logs[$i].level: Invalid value \"{$log['level']}\"";
                continue;
            }

            $dtos[] = new LogEntryDTO(
                timestamp: $log['timestamp'] ?? '',
                level:     $level,
                service:   $log['service'] ?? '',
                message:   $log['message'] ?? '',
                context:   $log['context'] ?? null,
                trace_id:  $log['trace_id'] ?? null,
            );

            foreach ($this->validator->validate($dtos) as $dto) {
                $errors[] = "logs[$i].{$dto->getPropertyPath()}: {$dto->getMessage()}";
            }
        }

        return [$dtos, $errors];
    }
}
