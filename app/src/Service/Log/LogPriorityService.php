<?php

declare(strict_types = 1);

namespace App\Service\Log;

final readonly class LogPriorityService
{
    private const array PRIORITIES = [
        'error'     => 7,
        'warning'   => 5,
        'info'      => 2,
        'debug'     => 1,
    ];

    public function getBatchPriority(array $dtos): int
    {
        $max = 0;
        foreach ($dtos as $dto) {
            $priority = self::PRIORITIES[$dto->level->value] ?? 0;
            if ($priority > $max) {
                $max = $priority;
            }
        }
        return $max;
    }
}
