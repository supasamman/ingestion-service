<?php

declare(strict_types=1);

namespace App\Exception;

final class MessageBusException extends \RuntimeException
{
    public function __construct(string $batchId, ?\Throwable $previous = null)
    {
        parent::__construct(message: "Failed to publish batch \"{$batchId}\" to queue", previous: $previous);
    }
}
