<?php

declare(strict_types=1);

namespace Coolycow\Dadata;

use Coolycow\Dadata\Exception\DadataException;
use Coolycow\Dadata\Exception\RequestException;
use Coolycow\Dadata\Http\AbstractClient;
use Coolycow\Dadata\Response\AbstractResponse;
use Coolycow\Dadata\Response\Address;
use Coolycow\Dadata\Response\Date;
use Coolycow\Dadata\Response\Email;
use Coolycow\Dadata\Response\Name;
use Coolycow\Dadata\Response\Passport;
use Coolycow\Dadata\Response\Phone;
use Coolycow\Dadata\Response\Statistics;
use Coolycow\Dadata\Response\StatisticServices;
use Coolycow\Dadata\Response\Vehicle;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use GuzzleHttp\ClientInterface;
use ReflectionClass;
use ReflectionProperty;

class ClientClean extends AbstractClient
{
    /**
     * Исходное значение распознано уверенно. Не требуется ручная проверка.
     */
    public const QC_OK = 0;

    /**
     * Исходное значение распознано с допущениями или не распознано. Требуется ручная проверка.
     */
    public const QC_UNSURE = 1;

    /**
     * Исходное значение пустое или заведомо "мусорное".
     */
    public const QC_INVALID = 2;

    protected string $version = 'v2';

    protected string $baseUrl = 'https://dadata.ru/api';

    protected string $baseUrlGeolocation = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/detectAddressByIp';

    /**
     * @param array<string, mixed> $httpOptions
     */
    public function __construct(
        ?string $token = null,
        ?string $secret = null,
        ?ClientInterface $httpClient = null,
        array $httpOptions = [],
    ) {
        parent::__construct($token, $secret, $httpClient, $httpOptions);
    }

    public function cleanAddress(string $address): Address
    {
        $response = $this->queryCleanItem($this->prepareUri('clean/address'), [$address]);

        return $this->populate(new Address(), $response);
    }

    public function cleanPhone(string $phone): Phone
    {
        $response = $this->queryCleanItem($this->prepareUri('clean/phone'), [$phone]);

        return $this->populate(new Phone(), $response);
    }

    public function cleanPassport(string $passport): Passport
    {
        $response = $this->queryCleanItem($this->prepareUri('clean/passport'), [$passport]);

        return $this->populate(new Passport(), $response);
    }

    public function cleanName(string $name): Name
    {
        $response = $this->queryCleanItem($this->prepareUri('clean/name'), [$name]);

        return $this->populate(new Name(), $response);
    }

    public function cleanEmail(string $email): Email
    {
        $response = $this->queryCleanItem($this->prepareUri('clean/email'), [$email]);

        return $this->populate(new Email(), $response);
    }

    public function cleanDate(string $date): Date
    {
        $response = $this->queryCleanItem($this->prepareUri('clean/birthdate'), [$date]);

        return $this->populate(new Date(), $response);
    }

    public function cleanVehicle(string $vehicle): Vehicle
    {
        $response = $this->queryCleanItem($this->prepareUri('clean/vehicle'), [$vehicle]);

        return $this->populate(new Vehicle(), $response);
    }

    public function getBalance(): float
    {
        $response = $this->request(self::METHOD_GET, $this->prepareUri('profile/balance'), [], null, true);

        if (!array_key_exists('balance', $response)) {
            throw new DadataException('Unexpected balance response: missing "balance" key.');
        }

        return (float) $response['balance'];
    }

    public function getStatistics(string|DateTimeInterface|null $date = null): Statistics
    {
        $response = $this->request(
            self::METHOD_GET,
            $this->prepareUri('stat/daily') . self::formatDateQuery($date),
            [],
            null,
            true,
        );

        return $this->populate(new Statistics(), $response);
    }

    public function detectAddressByIp(string $ip): ?Address
    {
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            throw new RequestException('Invalid IP address: ' . $ip);
        }

        $url = $this->baseUrlGeolocation . '?ip=' . rawurlencode($ip);
        $result = $this->request(self::METHOD_GET, $url);

        if (!array_key_exists('location', $result)) {
            throw new DadataException('Required key "location" is missing');
        }

        if ($result['location'] === null) {
            return null;
        }

        if (!is_array($result['location']) || !array_key_exists('data', $result['location'])) {
            throw new DadataException('Required key "data" is missing');
        }

        if ($result['location']['data'] === null) {
            return null;
        }

        if (!is_array($result['location']['data'])) {
            throw new DadataException('Unexpected location data format');
        }

        return $this->populate(new Address(), $result['location']['data']);
    }

    protected function prepareUri(string $endpoint): string
    {
        return $this->baseUrl . '/' . $this->version . '/' . $endpoint;
    }

    /**
     * @param list<string> $params
     *
     * @return array<string, mixed>
     */
    protected function queryCleanItem(string $uri, array $params): array
    {
        $result = $this->request(self::METHOD_POST, $uri, [], $params, true);

        if ($result === []) {
            throw new DadataException('Empty result');
        }

        // Clean API returns a list with one item for single-value clean requests.
        if (array_is_list($result)) {
            $item = $result[0] ?? null;

            if (!is_array($item)) {
                throw new DadataException('Unexpected clean response format');
            }

            /** @var array<string, mixed> $item */
            return $item;
        }

        /** @var array<string, mixed> $result */
        return $result;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @template T of AbstractResponse
     *
     * @param T $object
     *
     * @return T
     */
    protected function populate(AbstractResponse $object, array $data): AbstractResponse
    {
        $reflect = new ReflectionClass($object);
        $properties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            if (!array_key_exists($property->name, $data)) {
                continue;
            }

            $object->{$property->name} = $this->getValueByAnnotatedType($property, $data[$property->name]);
        }

        return $object;
    }

    protected function getValueByAnnotatedType(ReflectionProperty $property, mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        $comment = $property->getDocComment();

        if ($comment === false || !preg_match('/@var\s+([^\s]+)/', $comment, $matches)) {
            return $value;
        }

        $type = ltrim(explode('|', $matches[1])[0]);

        return match ($type) {
            'integer', 'int' => (int) $value,
            'float' => (float) $value,
            'StatisticServices' => $this->hydrateStatisticServices(is_array($value) ? $value : []),
            'DateTime', 'DateTimeInterface', 'DateTimeImmutable' => $this->hydrateDateTime($value),
            default => $value,
        };
    }

    /**
     * @param array<string, mixed> $value
     */
    protected function hydrateStatisticServices(array $value): StatisticServices
    {
        $result = new StatisticServices();
        $result->clean = isset($value['clean']) ? (int) $value['clean'] : 0;
        $result->merging = isset($value['merging']) ? (int) $value['merging'] : 0;
        $result->suggestions = isset($value['suggestions']) ? (int) $value['suggestions'] : 0;

        return $result;
    }

    protected function hydrateDateTime(mixed $value): ?DateTimeImmutable
    {
        if ($value instanceof DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($value);
        }

        if (!is_string($value) || $value === '') {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value, new DateTimeZone('Europe/Moscow'));

        return $date instanceof DateTimeImmutable ? $date : null;
    }

    private static function formatDateQuery(string|DateTimeInterface|null $val): string
    {
        if ($val === null || $val === '') {
            return '';
        }

        if (is_string($val)) {
            return '?date=' . rawurlencode($val);
        }

        return '?date=' . $val->format('Y-m-d');
    }
}
