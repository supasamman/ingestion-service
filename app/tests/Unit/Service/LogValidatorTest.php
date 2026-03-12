<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

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

        $this->validator = new LogValidatorService($validator);
    }

    public function testValidLogReturnsAtosAndNoErrors(): void
    {
        [$dtos, $errors] = $this->validator->validate([
            [
                'timestamp' => '2026-02-26T10:30:45Z',
                'level' => 'error',
                'service' => 'auth-service',
                'message' => 'Something failed',
            ],
        ]);

        $this->assertEmpty($errors);
        $this->assertCount(1, $dtos);
    }

    public function testMissingRequiredFieldsReturnsErrors(): void
    {
        [$dtos, $errors] = $this->validator->validate([
            ['level' => 'error'],
        ]);

        $this->assertNotEmpty($errors);
    }

    public function testInvalidLevelReturnsError(): void
    {
        [$dtos, $errors] = $this->validator->validate([
            [
                'timestamp' => '2026-02-26T10:30:45Z',
                'level' => 'invalid_level',
                'service' => 'auth-service',
                'message' => 'test',
            ],
        ]);

        $this->assertNotEmpty($errors);
    }
}
