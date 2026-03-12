<?php

declare(strict_types = 1);

namespace App\Tests\Integration\Controller;

use App\Contract\LogIngestionServiceInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LogIngestionControllerTest extends WebTestCase
{
    private function getValidLog(): array
    {
        return [
            'timestamp' => '2026-02-26T10:30:45Z',
            'level'     => 'error',
            'service'   => 'auth-service',
            'message'   => 'Auth failed',
        ];
    }

    public function testValidRequestReturns202(): void
    {
        $client = static::createClient();

        $mock = $this->createStub(LogIngestionServiceInterface::class);
        $mock->method('ingest')->willReturn('batch_123');
        $client->getContainer()->set(LogIngestionServiceInterface::class, $mock);

        $client->request('POST', '/api/logs/ingest', [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['logs' => [$this->getValidLog()]])
        );

        $this->assertResponseStatusCodeSame(202);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('accepted', $response['status']);
        $this->assertEquals(1, $response['logs_count']);
    }

    public function testMissingLogsKeyReturns400(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/logs/ingest', [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['foo' => 'bar'])
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testInvalidLevelReturns400(): void
    {
        $client = static::createClient();

        $log = $this->getValidLog();
        $log['level'] = 'invalid_level';

        $client->request('POST', '/api/logs/ingest', [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['logs' => [$log]])
        );

        $this->assertResponseStatusCodeSame(400);
    }
}
