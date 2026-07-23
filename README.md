# Laravel DaData

Пакет для работы с [DaData.ru](https://dadata.ru) в Laravel 10–13 (PHP 8.2+; для Laravel 13 нужен PHP 8.3+).

DaData — сервис автоматической проверки и исправления контактных данных: ФИО, адреса, телефоны, email, паспорта и реквизиты компаний.

## Требования

- PHP `^8.2` (Laravel 13 — PHP `^8.3`)
- Laravel / Illuminate `^10|^11|^12|^13`
- Guzzle `^7.8`

## Установка

```bash
composer require coolycow/laravel-dadata
```

Опубликовать конфиг:

```bash
php artisan vendor:publish --provider="Coolycow\Dadata\DadataServiceProvider"
```

Задать ключи в `.env`:

```env
DADATA_TOKEN=your_token
DADATA_SECRET=your_secret
```

`DADATA_SECRET` нужен для API стандартизации (Clean). Для Suggest достаточно токена.

## Использование

### Подсказки (Suggest)

```php
use Coolycow\Dadata\Facades\DadataSuggest;

// Всегда возвращает list<array> (пустой список, если совпадений нет)
$result = DadataSuggest::suggest('address', ['query' => 'Москва', 'count' => 2]);

$party = DadataSuggest::partyById('7707083893', ['branch_type' => 'MAIN']);
```

Типы: `fio`, `address`, `party`, `email`, `bank` и другие справочники DaData.

### Стандартизация (Clean)

```php
use Coolycow\Dadata\Facades\DadataClean;

$address = DadataClean::cleanAddress('мск сухонска 11/-89');
$phone = DadataClean::cleanPhone('тел 7165219 доб139');
$passport = DadataClean::cleanPassport('4509 235857');
$name = DadataClean::cleanName('Срегей владимерович иванов');
$email = DadataClean::cleanEmail('serega@yandex/ru');
$date = DadataClean::cleanDate('24/3/12');
$vehicle = DadataClean::cleanVehicle('форд фокус');

$balance = DadataClean::getBalance();
$stats = DadataClean::getStatistics();
$stats = DadataClean::getStatistics('2022-12-01');

$byIp = DadataClean::detectAddressByIp('8.8.8.8'); // Address|null
```

### DI без фасадов

```php
use Coolycow\Dadata\ClientClean;
use Coolycow\Dadata\ClientSuggest;

public function __construct(
    private ClientSuggest $suggest,
    private ClientClean $clean,
) {}
```

Клиенты зарегистрированы как singleton и принимают `ClientInterface` в конструкторе — удобно для тестов.

### Исключения

| Класс | Когда |
|-------|--------|
| `Coolycow\Dadata\Exception\AuthenticationException` | нет/неверный токен или secret, HTTP 401/403 |
| `Coolycow\Dadata\Exception\RequestException` | пустой запрос, HTTP 400/404/405/413 |
| `Coolycow\Dadata\Exception\RateLimitException` | HTTP 429 |
| `Coolycow\Dadata\Exception\ServerException` | HTTP 500 |
| `Coolycow\Dadata\Exception\DadataException` | прочие ошибки транспорта/парсинга |

## Разработка

```bash
composer install
composer test
composer phpstan
```

## Ссылки

- https://dadata.ru
- https://dadata.ru/api
- https://github.com/gietos/dadata
