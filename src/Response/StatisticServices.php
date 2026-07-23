<?php

declare(strict_types=1);

namespace Coolycow\Dadata\Response;

/**
 * Агрегированная статистика.
 */
class StatisticServices
{
    /**
     * Поиск дублей.
     */
    public int $merging = 0;

    /**
     * Подсказки.
     */
    public int $suggestions = 0;

    /**
     * Стандартизация.
     */
    public int $clean = 0;
}
