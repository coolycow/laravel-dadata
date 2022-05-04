# Laravel DaData

Пакет работы с сервисом [DaData.ru](https://dadata.ru).

Dadata - сервис автоматической проверки и исправления контактных данных: ФИО, адреса, телефоны, email, паспорта и реквизиты компаний.

## Установка
Запустить:
```bash
composer require "coolycow/laravel-dadata"
```
---

__Для Laravel < 5.5:__
Зарегистрировать service-provider в config/app.php:
```php
  Coolycow\Dadata\DadataServiceProvider::class,
```

Для Lumen добавить в bootstrap/app.php:
```php
$app->withFacades();
```
---

Опубликовать конфиг: 
```bash
php artisan vendor:publish --provider="Coolycow\Dadata\DadataServiceProvider"
```

Задать токен (и ключ для API стандартизации) в `config/dadata.php` или `.env`
```php
    'token' => env('DADATA_TOKEN', ''),
    'secret' => env('DADATA_SECRET', ''),
```

## Использование
### Сервис подсказок (https://dadata.ru/api/suggest/)
API подсказок помогает человеку быстро ввести корректные данные. Подсказывает ФИО, email, почтовые адреса, реквизиты компаний и банков, и другие справочники.

Добавить в необходимый клас фасад:
```php
use Coolycow\Dadata\Facades\DadataSuggest;
```

#### Пример использование метода с параметрами:
```php
$result = DadataSuggest::suggest("address", ["query"=>"Москва", "count"=>2]);
print_r($result);
```
Первым параметром может быть: `fio, address, party, email, bank`

#### Пример использование [поиска по ИНН или ОГРН](https://dadata.ru/api/find-party/) с параметрами:
```php
$result = DadataSuggest::partyById('5077746329876', ["branch_type"=>"MAIN"]);
print_r($result);
```
Первым параметром может быть ИНН, ОГРН или Dadata HID

### Сервис стандартизации (https://dadata.ru/api/clean/)
API стандартизации приводит в порядок и обогащает дополнительной информацией почтовые адреса, телефоны, паспорта, ФИО и email.

Добавить в клас фасад:
```php
use Coolycow\Dadata\Facades\DadataClean;
```

Использовать методы: 
```php
$response = DadataClean::cleanAddress('мск сухонска 11/-89');
$response = DadataClean::cleanPhone('тел 7165219 доб139');
$response = DadataClean::cleanPassport('4509 235857');
$response = DadataClean::cleanName('Срегей владимерович иванов');
$response = DadataClean::cleanEmail('serega@yandex/ru');
$response = DadataClean::cleanDate('24/3/12');
$response = DadataClean::cleanVehicle('форд фокус');
$response = DadataClean::getStatistics();
$response = DadataClean::getStatistics(now()->subDays(6));
print_r($response);
```

### Проверка баланса системы
```php
$response = DadataClean::getBalance();
```

### Получение статистики использования сервиса
#### На текущий день
```php
$response = DadataClean::getStatistics();
```

#### На любую другую дату
```php
$response = DadataClean::getStatistics(now()->subDays(6));
// or
$response = DadataClean::getStatistics('2022-12-01');
```

## Ссылки, документация, API:
- https://dadata.ru
- https://dadata.ru/api
- https://github.com/gietos/dadata
