<?php

declare(strict_types=1);

namespace App\Contract;

interface LogIngestionServiceInterface
{
    public function ingest(array $logs): string;
}
