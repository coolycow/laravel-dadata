<?php

declare(strict_types=1);

namespace Coolycow\Dadata\Response;

/**
 * Идентификатор города по справочнику СДЭК.
 */
class Cdek extends AbstractResponse
{
    /**
     * @var string|null КЛАДР-код города.
     */
    public ?string $kladr_id = null;

    /**
     * @var string|null ФИАС-код города.
     */
    public ?string $fias_id = null;

    /**
     * @var string|null Идентификатор города по справочнику СДЭК.
     */
    public ?string $cdek_id = null;

    public function __toString(): string
    {
        return (string) $this->cdek_id;
    }
}
