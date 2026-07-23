<?php

declare(strict_types=1);

namespace Coolycow\Dadata\Http;

use Coolycow\Dadata\Exception\AuthenticationException;
use Coolycow\Dadata\Exception\DadataException;
use Coolycow\Dadata\Exception\RateLimitException;
use Coolycow\Dadata\Exception\RequestException;
use Coolycow\Dadata\Exception\ServerException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use JsonException;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractClient
{
    public const METHOD_GET = 'GET';

    public const METHOD_POST = 'POST';

    protected ClientInterface $httpClient;

    /** @var array<string, mixed> */
    protected array $httpOptions = [];

    protected string $token;

    protected ?string $secret = null;

    /**
     * @param array<string, mixed> $httpOptions
     */
    public function __construct(
        ?string $token = null,
        ?string $secret = null,
        ?ClientInterface $httpClient = null,
        array $httpOptions = [],
    ) {
        $config = ($token === null || $secret === null) ? $this->loadPackageConfig() : [];

        $this->token = $token ?? (string) ($config['token'] ?? '');
        $this->secret = $secret ?? (isset($config['secret']) ? (string) $config['secret'] : null);
        $this->httpClient = $httpClient ?? new Client();
        $this->httpOptions = array_merge(['http_errors' => false], $httpOptions);
    }

    /**
     * @return array<string, mixed>
     */
    protected function loadPackageConfig(): array
    {
        if (!function_exists('app')) {
            return [];
        }

        try {
            $app = app();
        } catch (\Throwable) {
            return [];
        }

        if (!$app->bound('config')) {
            return [];
        }

        /** @var array<string, mixed> $config */
        $config = (array) $app['config']->get('dadata', []);

        return $config;
    }

    /**
     * @param array<string, string> $headers
     * @param array<mixed>|null $body
     *
     * @return array<mixed>
     */
    protected function request(
        string $method,
        string $url,
        array $headers = [],
        ?array $body = null,
        bool $requireSecret = false,
    ): array {
        if ($this->token === '') {
            throw new AuthenticationException('Missing API token. Set DADATA_TOKEN in your environment.');
        }

        if ($requireSecret && ($this->secret === null || $this->secret === '')) {
            throw new AuthenticationException('Missing API secret. Set DADATA_SECRET in your environment.');
        }

        $defaultHeaders = [
            'Accept' => 'application/json',
            'Authorization' => 'Token ' . $this->token,
        ];

        if ($requireSecret) {
            $defaultHeaders['X-Secret'] = (string) $this->secret;
        }

        if ($body !== null) {
            $defaultHeaders['Content-Type'] = 'application/json';
        }

        try {
            $payload = $body !== null
                ? json_encode($body, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)
                : null;
        } catch (JsonException $exception) {
            throw new RequestException('Failed to encode request body: ' . $exception->getMessage(), 0, $exception);
        }

        $request = new Request($method, $url, array_merge($defaultHeaders, $headers), $payload);

        try {
            $response = $this->httpClient->send($request, $this->httpOptions);
        } catch (GuzzleException $exception) {
            throw new DadataException('HTTP request failed: ' . $exception->getMessage(), 0, $exception);
        }

        return $this->decodeResponse($response);
    }

    /**
     * @return array<mixed>
     */
    protected function decodeResponse(ResponseInterface $response): array
    {
        $statusCode = $response->getStatusCode();
        $rawBody = (string) $response->getBody();

        if ($statusCode !== 200) {
            $this->throwForStatus($statusCode);
        }

        if ($rawBody === '') {
            return [];
        }

        try {
            $decoded = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new DadataException(
                'Error parsing response: ' . $exception->getMessage(),
                0,
                $exception,
                $statusCode,
            );
        }

        if (!is_array($decoded)) {
            throw new DadataException('Unexpected response format: expected JSON object or array.', 0, null, $statusCode);
        }

        return $decoded;
    }

    protected function throwForStatus(int $statusCode): never
    {
        match ($statusCode) {
            400 => throw new RequestException('Incorrect request', $statusCode, null, $statusCode),
            401 => throw new AuthenticationException('Missing API key', $statusCode, null, $statusCode),
            403 => throw new AuthenticationException('Incorrect API key', $statusCode, null, $statusCode),
            404 => throw new RequestException('Not found', $statusCode, null, $statusCode),
            405 => throw new RequestException('Request method is not allowed', $statusCode, null, $statusCode),
            413 => throw new RequestException('Request entity too large / limits exceeded', $statusCode, null, $statusCode),
            429 => throw new RateLimitException('Too many requests', $statusCode, null, $statusCode),
            500 => throw new ServerException('Server internal error', $statusCode, null, $statusCode),
            default => throw new DadataException('Unexpected error', $statusCode, null, $statusCode),
        };
    }
}
