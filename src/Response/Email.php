<?php

declare(strict_types=1);

namespace Coolycow\Dadata\Response;

class Email extends AbstractResponse
{
    /**
     * Корректное значение.
     * Соответствует общепринятым правилам,
     * реальное существование адреса не проверяется.
     */
    public const QC_OK = 0;

    /**
     * Некорректное значение.
     * Не соответствует общепринятым правилам.
     */
    public const QC_INVALID = 1;

    /**
     * Пустое или заведомо «мусорное» значение.
     */
    public const QC_EMPTY = 2;

    /**
     * «Одноразовый» адрес.
     * Домены 10minutemail.com, getairmail.com, temp-mail.ru и аналогичные.
     */
    public const QC_DISPOSABLE = 3;

    /**
     * Исправлены опечатки.
     */
    public const QC_CORRECTED = 4;

    /**
     * @var string|null Исходный email.
     */
    public ?string $source = null;

    /**
     * @var string|null Стандартизованный email.
     */
    public ?string $email = null;

    public function __toString(): string
    {
        return (string) $this->email;
    }
}
