<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller;

use App\Contract\LogIngestionServiceInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class LogIngestionControllerTest extends WebTestCase
{
    private function getValidLog(): array
    {
        return [
            'timestamp' => '2026-02-26T10:30:45Z',
            'level' => 'error',
            'service' => 'auth-service',
            'message' => 'Auth failed',
        ];
    }

    public function testValidRequestReturns202(): void
    {
        $client = self::createClient();

        $mock = self::createStub(LogIngestionServiceInterface::class);
        $mock->method('ingest')->willReturn('batch_123');
        $client->getContainer()->set(LogIngestionServiceInterface::class, $mock);

        $client->request(
            method: 'POST',
            uri: '/api/logs/ingest',
            parameters: [],
            files: [],
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(value: ['logs' => [$this->getValidLog()]]),
        );

        $this->assertResponseStatusCodeSame(202);
        $response = json_decode(json: $client->getResponse()->getContent(), associative: true);
        self::assertEquals('accepted', $response['status']);
        self::assertEquals(1, $response['logs_count']);
    }

    public function testMissingLogsKeyReturns400(): void
    {
        $client = self::createClient();

        $client->request(
            method: 'POST',
            uri: '/api/logs/ingest',
            parameters: [],
            files: [],
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(value: ['foo' => 'bar']),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testInvalidLevelReturns400(): void
    {
        $client = self::createClient();

        $log = $this->getValidLog();
        $log['level'] = 'invalid_level';

        $client->request(
            method: 'POST',
            uri: '/api/logs/ingest',
            parameters: [],
            files: [],
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(value: ['logs' => [$log]]),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
