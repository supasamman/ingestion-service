<?php

declare(strict_types=1);

namespace App\Exception;

final class LogValidationException extends \RuntimeException
{
    public function __construct(private readonly array $errors)
    {
        parent::__construct(message: 'Log validation failed.');
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
