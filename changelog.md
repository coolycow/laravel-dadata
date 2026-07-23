# Changelog for Laravel DaData

## 2.1.0

- Поддержка Laravel 13 (`illuminate/support ^13.0`, `orchestra/testbench ^11.0`)
- CI matrix: Laravel 13 на PHP 8.3+

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
