<?php

namespace Coolycow\Dadata;

use DateTime;
use DateTimeZone;
use Exception;
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
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use RuntimeException;

/**
 * Class Client
 */
class ClientClean
{
    /**
     * Исходное значение распознано уверенно. Не требуется ручная проверка.
     */
    const QC_OK = 0;
    /**
     * Исходное значение распознано с допущениями или не распознано. Требуется ручная проверка.
     */
    const QC_UNSURE = 1;
    /**
     * Исходное значение пустое или заведомо "мусорное".
     */
    const QC_INVALID = 2;

    const METHOD_GET = 'GET';

    const METHOD_POST = 'POST';

    /**
     * @var string
     */
    protected $version = 'v2';

    /**
     * @var string
     */
    protected $baseUrl = 'https://dadata.ru/api';

    protected $baseUrlGeolocation = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/detectAddressByIp';

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @var array
     */
    protected $httpOptions = [];

    public function __construct()
    {
        $this->httpClient = new Client();
        $this->config = config('dadata');

        foreach ($this->config as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * Cleans address.
     *
     * @param string $address
     *
     * @return Address
     * @throws GuzzleException
     * @throws ReflectionException
     */
    public function cleanAddress($address)
    {
        $response = $this->query($this->prepareUri('clean/address'), [$address]);
        $result = $this->populate(new Address, $response);

        if (!$result instanceof Address) {
            throw new RuntimeException('Unexpected populate result: ' . get_class($result) . '. Expected: ' . Address::class);
        }

        return $result;
    }

    /**
     * Requests API.
     *
     * @param string $uri
     * @param array  $params
     *
     * @param string $method
     *
     * @return array
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    protected function query($uri, array $params = [], $method = self::METHOD_POST)
    {
        $request = new Request($method, $uri, [
            'Content-Type' => 'application/json',
            'Authorization' => 'Token ' . $this->token,
            'X-Secret' => $this->secret,
        ], 0 < count($params) ? json_encode($params) : null);

        $response = $this->httpClient->send($request, $this->httpOptions);

        $result = json_decode($response->getBody(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Error parsing response: ' . json_last_error_msg());
        }

        if (empty($result)) {
            throw new RuntimeException('Empty result');
        }

        return (count($result) === 1) ? array_shift($result) : $result;
    }

    /**
     * Prepares URI for the request.
     *
     * @param string $endpoint
     * @return string
     */
    protected function prepareUri($endpoint)
    {
        return $this->baseUrl . '/' . $this->version . '/' . $endpoint;
    }

    /**
     * Populates object with data.
     *
     * @param AbstractResponse $object
     * @param array            $data
     * @return AbstractResponse
     * @throws ReflectionException
     */
    protected function populate(AbstractResponse $object, array $data)
    {
        $reflect = new ReflectionClass($object);

        $properties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            if (array_key_exists($property->name, $data)) {
                $object->{$property->name} = $this->getValueByAnnotatedType($property, $data[$property->name]);
            }
        }

        return $object;
    }

    /**
     * Guesses and converts property type by phpdoc comment.
     *
     * @param ReflectionProperty $property
     * @param mixed              $value
     * @return mixed
     */
    protected function getValueByAnnotatedType(ReflectionProperty $property, $value)
    {
        $comment = $property->getDocComment();
        $result = $value;
        if (preg_match('/@var (.+?)(\|null)? /', $comment, $matches)) {
            switch ($matches[1]) {
                case 'integer':
                case 'int':
                    $result = (int)$value;
                    break;
                case 'float':
                    $result = (float)$value;
                    break;
                case 'StatisticServices':
                    $result = new StatisticServices;
                    $result->clean = empty($value['clean']) ? 0 : (int)$value['clean'];
                    $result->merging = empty($value['merging']) ? 0 : (int)$value['merging'];
                    $result->suggestions = empty($value['suggestions']) ? 0 : (int)$value['suggestions'];
                    break;
                case 'DateTime':
                    $result = date_create_from_format('Y-m-d', $value, new DateTimeZone('Europe/Moscow'));
                    break;
            }
        }

        return $result;
    }

    /**
     * Cleans phone.
     *
     * @param string $phone
     *
     * @return Phone
     * @throws GuzzleException
     * @throws ReflectionException
     */
    public function cleanPhone($phone)
    {
        $response = $this->query($this->prepareUri('clean/phone'), [$phone]);
        $result = $this->populate(new Phone, $response);

        if (!$result instanceof Phone) {
            throw new RuntimeException('Unexpected populate result: ' . get_class($result) . '. Expected: ' . Phone::class);
        }
        return $result;
    }

    /**
     * Cleans passport.
     *
     * @param string $passport
     *
     * @return Passport
     * @throws GuzzleException
     * @throws ReflectionException
     */
    public function cleanPassport($passport)
    {
        $response = $this->query($this->prepareUri('clean/passport'), [$passport]);
        $result = $this->populate(new Passport(), $response);

        if (!$result instanceof Passport) {
            throw new RuntimeException('Unexpected populate result: ' . get_class($result) . '. Expected: ' . Passport::class);
        }

        return $result;
    }

    /**
     * Cleans name.
     *
     * @param string $name
     *
     * @return Name
     * @throws GuzzleException
     * @throws ReflectionException
     */
    public function cleanName($name)
    {
        $response = $this->query($this->prepareUri('clean/name'), [$name]);
        $result = $this->populate(new Name(), $response);

        if (!$result instanceof Name) {
            throw new RuntimeException('Unexpected populate result: ' . get_class($result) . '. Expected: ' . Name::class);
        }

        return $result;
    }

    /**
     * Cleans email.
     *
     * @param string $email
     *
     * @return Email
     * @throws GuzzleException
     * @throws ReflectionException
     */
    public function cleanEmail($email)
    {
        $response = $this->query($this->prepareUri('clean/email'), [$email]);
        $result = $this->populate(new Email, $response);

        if (!$result instanceof Email) {
            throw new RuntimeException('Unexpected populate result: ' . get_class($result) . '. Expected: ' . Email::class);
        }

        return $result;
    }

    /**
     * Cleans date.
     *
     * @param string $date
     *
     * @return Date
     * @throws GuzzleException
     * @throws ReflectionException
     */
    public function cleanDate($date)
    {
        $response = $this->query($this->prepareUri('clean/birthdate'), [$date]);
        $result = $this->populate(new Date, $response);

        if (!$result instanceof Date) {
            throw new RuntimeException('Unexpected populate result: ' . get_class($result) . '. Expected: ' . Date::class);
        }

        return $result;
    }

    /**
     * Cleans vehicle.
     *
     * @param string $vehicle
     *
     * @return Vehicle
     * @throws GuzzleException
     * @throws ReflectionException
     */
    public function cleanVehicle($vehicle)
    {
        $response = $this->query($this->prepareUri('clean/vehicle'), [$vehicle]);
        $result = $this->populate(new Vehicle, $response);

        if (!$result instanceof Vehicle) {
            throw new RuntimeException('Unexpected populate result: ' . get_class($result) . '. Expected: ' . Vehicle::class);
        }

        return $result;
    }

    /**
     * Gets balance.
     *
     * @return float
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public function getBalance()
    {
        $response = $this->query($this->prepareUri('profile/balance'), [], self::METHOD_GET);
        return (double)$response;
    }

    /**
     * Usage statistics
     *
     * @link https://dadata.ru/api/stat/
     * @param string|DateTime $date Дата, за которую возвращается статистика в формате Y-m-d
     * @return AbstractResponse
     * @throws GuzzleException
     * @throws ReflectionException
     */
    public function getStatistics($date = '')
    {
        $response = $this->query($this->prepareUri('stat/daily' . self::formatDateQuery($date)), [], self::METHOD_GET);
        $result = $this->populate(new Statistics, $response);

        if (!$result instanceof Statistics) {
            throw new RuntimeException('Unexpected populate result: ' . get_class($result) . '. Expected: ' . Statistics::class);
        }

        return $result;
    }

    /**
     * @param string $ip
     * @return null|Address
     * @throws Exception
     * @throws GuzzleException
     */
    public function detectAddressByIp($ip)
    {
        $request = new Request('get', $this->baseUrlGeolocation . '?ip=' . $ip, [
            'Accept' => 'application/json',
            'Authorization' => 'Token ' . $this->token,
        ]);

        $response = $this->httpClient->send($request);

        $result = json_decode($response->getBody(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Error parsing response: ' . json_last_error_msg());
        }

        if (!array_key_exists('location', $result)) {
            throw new Exception('Required key "location" is missing');
        }

        if (null === $result['location']) {
            return null;
        }

        if (!array_key_exists('data', $result['location'])) {
            throw new Exception('Required key "data" is missing');
        }

        if (null === $result['location']['data']) {
            return null;
        }

        $address = $this->populate(new Address, $result['location']['data']);

        if (!$address instanceof Address) {
            throw new RuntimeException('Unexpected populate result: ' . get_class($result) . '. Expected: ' . Address::class);
        }

        return $address;
    }

    /**
     * @param mixed $val
     * @return string
     */
    private static function formatDateQuery($val)
    {
        if (empty($val)) {
            return '';
        } elseif (is_string($val)) {
            return '?date=' . $val;
        } elseif ($val instanceof DateTime) {
            return '?date=' . $val->format('Y-m-d');
        } else {
            return '';
        }
    }
}
