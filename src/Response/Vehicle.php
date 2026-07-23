<?php

declare(strict_types=1);

namespace Coolycow\Dadata\Response;

class Vehicle extends AbstractResponse
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
     * @var string|null Стандартизованное значение.
     */
    public ?string $result = null;

    /**
     * @var string|null Марка.
     */
    public ?string $brand = null;

    /**
     * @var string|null Модель.
     */
    public ?string $model = null;

    public function __toString(): string
    {
        return (string) $this->result;
    }
}
