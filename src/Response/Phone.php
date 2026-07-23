<?php

declare(strict_types=1);

namespace Coolycow\Dadata\Response;

class Phone extends AbstractResponse
{
    /**
     * Телефон распознан уверенно.
     */
    public const QC_OK = 0;

    /**
     * Телефон распознан с допущениями или не распознан.
     */
    public const QC_INVALID = 1;

    /**
     * Телефон пустой или заведомо «мусорный».
     */
    public const QC_EMPTY = 2;

    /**
     * Обнаружено несколько телефонов, распознан первый.
     */
    public const QC_MULTIPLE = 3;

    /**
     * Телефон соответствует адресу.
     */
    public const QC_CONFLICT_OK = 0;

    /**
     * Города адреса и телефона отличаются.
     */
    public const QC_CITY_MISMATCH = 2;

    /**
     * Регионы адреса и телефона отличаются.
     */
    public const QC_REGION_MISMATCH = 3;

    /**
     * @var string|null Исходный телефон одной строкой.
     */
    public ?string $source = null;

    /**
     * @var string|null Тип телефона.
     */
    public ?string $type = null;

    /**
     * @var string|null Стандартизованный телефон одной строкой.
     */
    public ?string $phone = null;

    /**
     * @var string|null Код страны.
     */
    public ?string $country_code = null;

    /**
     * @var string|null Код города / DEF-код.
     */
    public ?string $city_code = null;

    /**
     * @var string|null Локальный номер телефона.
     */
    public ?string $number = null;

    /**
     * @var string|null Добавочный номер.
     */
    public ?string $extension = null;

    /**
     * @var string|null Оператор связи.
     */
    public ?string $provider = null;

    /**
     * @var string|null Регион.
     */
    public ?string $region = null;

    /**
     * @var string|null Часовой пояс.
     */
    public ?string $timezone = null;

    /**
     * @var int|null Признак конфликта телефона с адресом (see QC_CONFLICT_* constants).
     */
    public ?int $qc_conflict = null;

    public function __toString(): string
    {
        return (string) $this->phone;
    }
}
