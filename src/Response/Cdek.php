<?php

namespace Coolycow\Dadata\Response;

class Cdek extends AbstractResponse
{
    /**
     * @var string КЛАДР-код города.
     */
    public $kladr_id;

    /**
     * @var string ФИАС-код города.
     */
    public $fias_id;

    /**
     * @var string Идентификатор города по справочнику СДЭК.
     */
    public $cdek_id;

    public function __toString()
    {
        return (string) $this->cdek_id;
    }
}
