<?php

declare(strict_types = 1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class LogBatchRequestDTO
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Type('array')]
        #[Assert\Count(min: 1, max: 1000)]
        public ?array $logs = null,
    )
    {}

}
