<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Exception\LogValidationException;
use App\Service\Log\LogValidatorService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class LogValidatorTest extends TestCase
{
    private readonly LogValidatorService $validator;

    protected function setUp(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $this->validator = new LogValidatorService(validator: $validator);
    }

    public function testValidLogReturnsDtosWithoutErrors(): void
    {
        $dtos = $this->validator->validate(logs: [
            [
                'timestamp' => '2026-02-26T10:30:45Z',
                'level' => 'error',
                'service' => 'auth-service',
                'message' => 'Something failed',
            ],
        ]);

        $this->assertCount(1, $dtos);
    }

    public function testMissingRequiredFieldsReturnsErrors(): void
    {
        $this->expectException(LogValidationException::class);

        $this->validator->validate(logs: [
            ['level' => 'error'],
        ]);
    }

    public function testInvalidLevelReturnsError(): void
    {
        $this->expectException(LogValidationException::class);

        $this->validator->validate(logs: [
            [
                'timestamp' => '2026-02-26T10:30:45Z',
                'level' => 'invalid_level',
                'service' => 'auth-service',
                'message' => 'test',
            ],
        ]);
    }
}
