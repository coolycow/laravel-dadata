<?php

declare(strict_types=1);

namespace Coolycow\Dadata\Exception;

use RuntimeException;
use Throwable;

class DadataException extends RuntimeException
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        protected readonly ?int $statusCode = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }
}
