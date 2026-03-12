<?php

declare(strict_types = 1);

namespace App\MessageHandler;

use App\Message\LogBatchMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class LogBatchMessageHandler
{
    public function __invoke(LogBatchMessage $message): void
    {
        // пока просто логируем что получили
        dump($message->batchId, count($message->logs));
    }
}
