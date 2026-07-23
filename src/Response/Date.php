<?php

declare(strict_types=1);

namespace Coolycow\Dadata\Response;

class Date extends AbstractResponse
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
     * @var string|null Исходная дата.
     */
    public ?string $source = null;

    /**
     * @var string|null Стандартизованная дата.
     */
    public ?string $birthdate = null;

    public function __toString(): string
    {
        return (string) $this->birthdate;
    }
}
