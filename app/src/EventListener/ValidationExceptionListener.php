<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Enum\ResponseStatus;
use App\Exception\LogValidationException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener]
final readonly class ValidationExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof LogValidationException) {
            return;
        }

        $event->setResponse(response: new JsonResponse(data: [
            'status' => ResponseStatus::ERROR->value,
            'errors' => $exception->getErrors(),
        ], status: Response::HTTP_BAD_REQUEST));
    }
}
