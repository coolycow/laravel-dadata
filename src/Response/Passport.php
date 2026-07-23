<?php

declare(strict_types=1);

namespace Coolycow\Dadata\Response;

class Passport extends AbstractResponse
{
    /**
     * Действующий паспорт.
     */
    public const QC_OK = 0;

    /**
     * Неправильный формат серии или номера.
     */
    public const QC_WRONG_FORMAT = 1;

    /**
     * Исходное значение пустое.
     */
    public const QC_EMPTY_SOURCE = 2;

    /**
     * Недействительный паспорт.
     */
    public const QC_INVALID = 10;

    /**
     * @var string|null Исходная серия и номер одной строкой.
     */
    public ?string $source = null;

    /**
     * @var string|null Серия.
     */
    public ?string $series = null;

    /**
     * @var string|null Номер.
     */
    public ?string $number = null;

    public function __toString(): string
    {
        return trim(implode(' ', array_filter([$this->series, $this->number], static fn ($v) => $v !== null && $v !== '')));
    }
}
