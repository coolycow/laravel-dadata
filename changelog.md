# Changelog for Laravel DaData

## 2.1.0

- Поддержка Laravel 13 (`illuminate/support ^13.0`, `orchestra/testbench ^11.0`)
- CI matrix: Laravel 10–13 с явным маппингом Testbench; для EOL L10/L11 отключён Composer advisory block
- Минимальные версии Testbench: `^8.37|^9.17|^10.11|^11.0`

## 2.0.0

- Требования: PHP `^8.2`, Laravel `^10|^11|^12`, Guzzle `^7.8`
- Единый HTTP-слой с маппингом статусов (включая 429)
- Иерархия исключений (`DadataException` и наследники)
- Suggest всегда возвращает `list` (пустой список вместо исключения)
- DI: injectable `ClientInterface`, singleton-привязки, typed properties / `strict_types`
- Исправления: null не кастится в `0`, валидация IP, `get_class` bug, объявление config
- PHPUnit + MockHandler, PHPStan, GitHub Actions CI

## 1.0.0 (2022-05-04)

- Создание пакета
