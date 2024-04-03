<?php

namespace Coolycow\Dadata;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use RuntimeException;

/**
 * Class ClientHint
 *
 * @package \Coolycow\Dadata
 */
class ClientSuggest
{

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    /**
     * Организации.
     */
    const TYPE_PARTY = 'party';

    /**
     * Организации (алиас).
     */
    const TYPE_ORG = 'party';

    /**
     * Адреса.
     */
    const TYPE_ADDRESS = 'address';

    /**
     * Банки.
     */
    const TYPE_BANK = 'bank';

    /**
     * ФИО.
     */
    const TYPE_FIO = 'fio';

    /**
     * Email.
     */
    const TYPE_EMAIL = 'email';

    /**
     * Кем выдан паспорт.
     */
    const TYPE_FMS_UNIT = 'fms_unit';

    /**
     * Налоговые инспекции.
     */
    const TYPE_FNS_UNIT = 'fns_unit';

    /**
     * Отделения Почты России.
     */
    const TYPE_POSTAL_OFFICE = 'postal_office';

    /**
     * Мировые суды.
     */
    const TYPE_REGION_COURT = 'region_court';

    /**
     * Страны.
     */
    const TYPE_COUNTRY = 'country';

    /**
     * Валюты.
     */
    const TYPE_CURRENCY = 'currency';

    /**
     * Виды деятельности (ОКВЭД 2).
     */
    const TYPE_OKVED_2 = 'okved2';

    /**
     * Виды продукции (ОКПД 2).
     */
    const TYPE_OKPD_2 = 'okpd2';

    /**
     * API-ключ.
     *
     * @var string
     */
    protected $token;

    /**
     * Настройки.
     *
     * @var array
     */
    protected $config;

    /**
     * Версия API.
     *
     * @var string
     */
    protected $version = '4_1';

    /**
     * Базовый адрес.
     *
     * @var string
     */
    protected $base_url = 'https://suggestions.dadata.ru/suggestions/api';

    /**
     * URI подсказок.
     *
     * @var string
     */
    protected $url_suggestions = 'rs/suggest';

    /**
     * URI поиска организации по ИНН, ОГРН, HID.
     *
     * @var string
     */
    protected $url_findById = 'rs/findById/party';

    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @var array
     */
    protected $httpOptions = [];

    /**
     * ClientHint constructor.
     */
    public function __construct()
    {
        $this->config = config('dadata');
        $this->token = $this->config['token'];
        $this->httpClient = new Client();
    }

    /**
     * Организация по ИНН или ОГРН.
     *
     * @link https://dadata.ru/api/find-party/
     * @param string $id     ИНН, ОГРН, Dadata HID.
     * @param array  $params Дополнительные параметры.
     * @return mixed
     */
    public function partyById($id, array $params = [])
    {
        $params['query'] = $id;

        return $this->query("{$this->base_url}/{$this->version}/{$this->url_findById}", $params);
    }

    /**
     * Requests API.
     *
     * @param string $url
     * @param array  $params
     * @param string $method
     * @return mixed
     */
    protected function query($url, array $params = [], $method = self::METHOD_POST)
    {

        if (empty($params['query'])) {
            throw new RuntimeException('Empty request');
        }

        $request = new Request($method, $url, [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Token ' . $this->token
        ], 0 < count($params) ? json_encode($params) : null);

        try {
            $response = $this->httpClient->send($request, $this->httpOptions);
        } catch (GuzzleException $guzzleException) {
            throw new RuntimeException('Http exception: ' . $guzzleException->getMessage());
        } catch (Exception $exception) {
            throw new RuntimeException('Exception: ' . $exception->getMessage());
        }

        switch ($response->getStatusCode()) {
            case 200:
                // успешный запрос
                $result = json_decode($response->getBody(), true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new RuntimeException('Error parsing response: ' . json_last_error_msg());
                }

                if (empty($result['suggestions'][0])) {
                    throw new RuntimeException('Empty result');
                }

                return count($result['suggestions']) === 1 ? $result['suggestions'][0] : $result['suggestions'];
            case 400:
                // DaData Некорректный запрос.
                throw new RuntimeException('Incorrect request');
            case 401:
                // В запросе отсутствует API-ключ.
                throw new RuntimeException('Missing API key');
            case 403:
                // В запросе указан несуществующий API-ключ.
                throw new RuntimeException('Incorrect API key');
            case 405:
                // Запрос сделан с методом, отличным от POST.
                throw new RuntimeException('Request method must be POST');
            case 413:
                // Нарушены ограничения.
                throw new RuntimeException('You push the limits of suggestions');
                break;
            case 500:
                // Произошла внутренняя ошибка сервиса во время обработки.
                throw new RuntimeException('Server internal error');
            default:
                throw new RuntimeException('Unexpected error');
        }

    }

    /**
     * Подсказки.
     *
     * @link https://dadata.ru/api/suggest/
     * @param string $type
     * @param array  $fields
     * @return bool|mixed|string
     */
    public function suggest($type, $fields)
    {
        return $this->query("{$this->base_url}/{$this->version}/{$this->url_suggestions}/$type", $fields);
    }

    /**
     * Подсказки по URL.
     *
     * @link https://dadata.ru/api/suggest/
     * @param string $url
     * @param string $type
     * @param array  $fields
     * @return bool|mixed|string
     */
    public function suggestByURL($url, $type, $fields)
    {
        return $this->query("$url/$type", $fields);
    }
}
