<?php

declare(strict_types=1);

namespace Coolycow\Dadata\Tests;

use Coolycow\Dadata\ClientSuggest;
use Coolycow\Dadata\Exception\AuthenticationException;
use Coolycow\Dadata\Exception\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class ClientSuggestTest extends TestCase
{
    public function testSuggestReturnsListEvenForSingleItem(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode([
                'suggestions' => [
                    ['value' => 'Москва', 'data' => ['city' => 'Москва']],
                ],
            ], JSON_THROW_ON_ERROR)),
        ]);

        $result = $client->suggest('address', ['query' => 'Москва']);

        self::assertCount(1, $result);
        self::assertSame('Москва', $result[0]['value']);
    }

    public function testSuggestReturnsEmptyListWhenNoMatches(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['suggestions' => []], JSON_THROW_ON_ERROR)),
        ]);

        $result = $client->suggest('address', ['query' => 'zzzzz']);

        self::assertSame([], $result);
    }

    public function testSuggestThrowsOnEmptyQuery(): void
    {
        $client = $this->makeClient([]);

        $this->expectException(RequestException::class);
        $client->suggest('address', ['query' => '']);
    }

    public function testMissingTokenThrowsAuthenticationException(): void
    {
        $client = new ClientSuggest(token: '');

        $this->expectException(AuthenticationException::class);
        $client->suggest('address', ['query' => 'Москва']);
    }

    public function testUnauthorizedStatusMapped(): void
    {
        $client = $this->makeClient([
            new Response(401, [], '{"message":"Unauthorized"}'),
        ]);

        $this->expectException(AuthenticationException::class);
        $client->suggest('address', ['query' => 'Москва']);
    }

    public function testPartyByIdSendsExpectedUrl(): void
    {
        $history = [];
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'suggestions' => [
                    ['value' => 'ООО Ромашка', 'data' => ['inn' => '7707083893']],
                ],
            ], JSON_THROW_ON_ERROR)),
        ]);
        $handler = HandlerStack::create($mock);
        $handler->push(Middleware::history($history));

        $client = new ClientSuggest(
            token: 'test-token',
            httpClient: new Client(['handler' => $handler, 'http_errors' => false]),
        );

        $client->partyById('7707083893');

        self::assertCount(1, $history);
        $request = $history[0]['request'];
        self::assertSame(
            'https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party',
            (string) $request->getUri(),
        );
        self::assertSame('Token test-token', $request->getHeaderLine('Authorization'));
    }

    /**
     * @param list<Response> $responses
     */
    private function makeClient(array $responses): ClientSuggest
    {
        $mock = new MockHandler($responses);
        $handler = HandlerStack::create($mock);

        return new ClientSuggest(
            token: 'test-token',
            httpClient: new Client(['handler' => $handler, 'http_errors' => false]),
        );
    }
}
