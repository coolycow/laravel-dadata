<?php

declare(strict_types=1);

namespace Coolycow\Dadata\Response;

class Name extends AbstractResponse
{
    /**
     * Исходное значение распознано уверенно.
     */
    public const QC_OK = 0;

    /**
     * Исходное значение распознано с допущениями или не распознано.
     */
    public const QC_INVALID = 1;

    /**
     * Исходное значение пустое или заведомо «мусорное».
     */
    public const QC_EMPTY = 2;

    /**
     * Пол мужской.
     */
    public const GENDER_MALE = 'М';

    /**
     * Пол женский.
     */
    public const GENDER_FEMALE = 'Ж';

    /**
     * Пол не удалось однозначно определить.
     */
    public const GENDER_UNKNOWN = 'НД';

    /**
     * @var string|null Исходные ФИО одной строкой.
     */
    public ?string $source = null;

    /**
     * @var string|null Стандартизованные ФИО одной строкой.
     */
    public ?string $result = null;

    /**
     * @var string|null ФИО в родительном падеже (кого?).
     */
    public ?string $result_genitive = null;

    /**
     * @var string|null ФИО в дательном падеже (кому?).
     */
    public ?string $result_dative = null;

    /**
     * @var string|null ФИО в творительном падеже (кем?).
     */
    public ?string $result_ablative = null;

    /**
     * @var string|null Фамилия.
     */
    public ?string $surname = null;

    /**
     * @var string|null Имя.
     */
    public ?string $name = null;

    /**
     * @var string|null Отчество.
     */
    public ?string $patronymic = null;

    /**
     * @var string|null Пол (see GENDER_* constants).
     */
    public ?string $gender = null;

    public function __toString(): string
    {
        return (string) $this->result;
    }
}
