<?php

declare(strict_types=1);

namespace Coolycow\Dadata\Response;

abstract class AbstractResponse
{
    /**
     * @var string|null Исходная строка.
     */
    public ?string $source = null;

    /**
     * @var int|null Код качества (see QC_* constants).
     */
    public ?int $qc = null;
}
