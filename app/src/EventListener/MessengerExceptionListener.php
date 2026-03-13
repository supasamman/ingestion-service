<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Enum\ResponseStatus;
use App\Exception\MessageBusException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener]
class MessengerExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof MessageBusException) {
            return;
        }

        $event->setResponse(response: new JsonResponse(data: [
            'status' => ResponseStatus::ERROR->value,
            'message' => 'Service unavailable',
        ], status: Response::HTTP_SERVICE_UNAVAILABLE));
    }
}
