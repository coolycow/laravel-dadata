<?php

declare(strict_types=1);

namespace Coolycow\Dadata;

use Coolycow\Dadata\Exception\RequestException;
use Coolycow\Dadata\Http\AbstractClient;
use GuzzleHttp\ClientInterface;

class ClientSuggest extends AbstractClient
{
    /**
     * Организации.
     */
    public const TYPE_PARTY = 'party';

    /**
     * Организации (алиас).
     */
    public const TYPE_ORG = 'party';

    /**
     * Адреса.
     */
    public const TYPE_ADDRESS = 'address';

    /**
     * Банки.
     */
    public const TYPE_BANK = 'bank';

    /**
     * ФИО.
     */
    public const TYPE_FIO = 'fio';

    /**
     * Email.
     */
    public const TYPE_EMAIL = 'email';

    /**
     * Кем выдан паспорт.
     */
    public const TYPE_FMS_UNIT = 'fms_unit';

    /**
     * Налоговые инспекции.
     */
    public const TYPE_FNS_UNIT = 'fns_unit';

    /**
     * Отделения Почты России.
     */
    public const TYPE_POSTAL_OFFICE = 'postal_office';

    /**
     * Мировые суды.
     */
    public const TYPE_REGION_COURT = 'region_court';

    /**
     * Страны.
     */
    public const TYPE_COUNTRY = 'country';

    /**
     * Валюты.
     */
    public const TYPE_CURRENCY = 'currency';

    /**
     * Виды деятельности (ОКВЭД 2).
     */
    public const TYPE_OKVED_2 = 'okved2';

    /**
     * Виды продукции (ОКПД 2).
     */
    public const TYPE_OKPD_2 = 'okpd2';

    protected string $version = '4_1';

    protected string $baseUrl = 'https://suggestions.dadata.ru/suggestions/api';

    protected string $urlSuggestions = 'rs/suggest';

    protected string $urlFindById = 'rs/findById/party';

    /**
     * @param array<string, mixed> $httpOptions
     */
    public function __construct(
        ?string $token = null,
        ?ClientInterface $httpClient = null,
        array $httpOptions = [],
    ) {
        parent::__construct($token, '', $httpClient, $httpOptions);
    }

    /**
     * Организация по ИНН или ОГРН.
     *
     * @link https://dadata.ru/api/find-party/
     *
     * @param array<string, mixed> $params
     *
     * @return list<array<string, mixed>>
     */
    public function partyById(string $id, array $params = []): array
    {
        $params['query'] = $id;

        return $this->query("{$this->baseUrl}/{$this->version}/{$this->urlFindById}", $params);
    }

    /**
     * Подсказки.
     *
     * @link https://dadata.ru/api/suggest/
     *
     * @param array<string, mixed> $fields
     *
     * @return list<array<string, mixed>>
     */
    public function suggest(string $type, array $fields): array
    {
        return $this->query("{$this->baseUrl}/{$this->version}/{$this->urlSuggestions}/{$type}", $fields);
    }

    /**
     * Подсказки по произвольному URL.
     *
     * @link https://dadata.ru/api/suggest/
     *
     * @param array<string, mixed> $fields
     *
     * @return list<array<string, mixed>>
     */
    public function suggestByURL(string $url, string $type, array $fields): array
    {
        return $this->query(rtrim($url, '/') . '/' . ltrim($type, '/'), $fields);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return list<array<string, mixed>>
     */
    protected function query(string $url, array $params = [], string $method = self::METHOD_POST): array
    {
        $query = $params['query'] ?? null;

        if (!is_scalar($query) || (string) $query === '') {
            throw new RequestException('Empty request');
        }

        $result = $this->request($method, $url, [], $params);

        $suggestions = $result['suggestions'] ?? [];

        if (!is_array($suggestions)) {
            return [];
        }

        /** @var list<array<string, mixed>> $list */
        $list = array_values(array_filter($suggestions, 'is_array'));

        return $list;
    }
}
