<?php

declare(strict_types=1);

namespace Coolycow\Dadata\Tests;

use Coolycow\Dadata\ClientClean;
use Coolycow\Dadata\Exception\AuthenticationException;
use Coolycow\Dadata\Exception\RateLimitException;
use Coolycow\Dadata\Exception\RequestException;
use Coolycow\Dadata\Response\Address;
use Coolycow\Dadata\Response\Phone;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class ClientCleanTest extends TestCase
{
    public function testCleanAddressHydratesDtoAndPreservesNulls(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode([
                [
                    'source' => 'мск сухонска 11',
                    'result' => 'г Москва, ул Сухонская, д 11',
                    'geo_lat' => null,
                    'geo_lon' => null,
                    'qc' => 0,
                    'flat_area' => null,
                ],
            ], JSON_THROW_ON_ERROR)),
        ]);

        $address = $client->cleanAddress('мск сухонска 11');

        self::assertInstanceOf(Address::class, $address);
        self::assertSame('г Москва, ул Сухонская, д 11', $address->result);
        self::assertNull($address->geo_lat);
        self::assertNull($address->geo_lon);
        self::assertNull($address->flat_area);
        self::assertSame(0, $address->qc);
    }

    public function testCleanPhone(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode([
                [
                    'source' => '7165219',
                    'phone' => '+7 495 716-52-19',
                    'qc' => 0,
                ],
            ], JSON_THROW_ON_ERROR)),
        ]);

        $phone = $client->cleanPhone('7165219');

        self::assertInstanceOf(Phone::class, $phone);
        self::assertSame('+7 495 716-52-19', $phone->phone);
    }

    public function testGetBalance(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['balance' => 42.5], JSON_THROW_ON_ERROR)),
        ]);

        self::assertSame(42.5, $client->getBalance());
    }

    public function testDetectAddressByIpValidatesAndEncodes(): void
    {
        $history = [];
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'location' => [
                    'value' => 'г Москва',
                    'data' => [
                        'city' => 'Москва',
                        'geo_lat' => '55.753215',
                        'geo_lon' => '37.622504',
                    ],
                ],
            ], JSON_THROW_ON_ERROR)),
        ]);
        $handler = HandlerStack::create($mock);
        $handler->push(Middleware::history($history));

        $client = new ClientClean(
            token: 'test-token',
            secret: 'test-secret',
            httpClient: new Client(['handler' => $handler, 'http_errors' => false]),
        );

        $address = $client->detectAddressByIp('127.0.0.1');

        self::assertInstanceOf(Address::class, $address);
        self::assertSame('Москва', $address->city);
        self::assertSame(55.753215, $address->geo_lat);
        self::assertStringContainsString('ip=127.0.0.1', (string) $history[0]['request']->getUri());
    }

    public function testDetectAddressByIpRejectsInvalidIp(): void
    {
        $client = $this->makeClient([]);

        $this->expectException(RequestException::class);
        $client->detectAddressByIp('1.2.3.4&foo=bar');
    }

    public function testDetectAddressByIpReturnsNullWhenLocationMissing(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['location' => null], JSON_THROW_ON_ERROR)),
        ]);

        self::assertNull($client->detectAddressByIp('8.8.8.8'));
    }

    public function testMissingSecretThrows(): void
    {
        $client = new ClientClean(token: 'token', secret: '');

        $this->expectException(AuthenticationException::class);
        $client->cleanAddress('test');
    }

    public function testRateLimitMapped(): void
    {
        $client = $this->makeClient([
            new Response(429, [], '{"message":"Too many requests"}'),
        ]);

        $this->expectException(RateLimitException::class);
        $client->cleanAddress('test');
    }

    public function testCleanSendsSecretHeader(): void
    {
        $history = [];
        $mock = new MockHandler([
            new Response(200, [], json_encode([['source' => 'a', 'result' => 'b', 'qc' => 0]], JSON_THROW_ON_ERROR)),
        ]);
        $handler = HandlerStack::create($mock);
        $handler->push(Middleware::history($history));

        $client = new ClientClean(
            token: 'tok',
            secret: 'sec',
            httpClient: new Client(['handler' => $handler, 'http_errors' => false]),
        );

        $client->cleanAddress('a');

        self::assertSame('Token tok', $history[0]['request']->getHeaderLine('Authorization'));
        self::assertSame('sec', $history[0]['request']->getHeaderLine('X-Secret'));
    }

    /**
     * @param list<Response> $responses
     */
    private function makeClient(array $responses): ClientClean
    {
        $mock = new MockHandler($responses);
        $handler = HandlerStack::create($mock);

        return new ClientClean(
            token: 'test-token',
            secret: 'test-secret',
            httpClient: new Client(['handler' => $handler, 'http_errors' => false]),
        );
    }
}
