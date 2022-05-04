<?php

namespace Coolycow\Dadata\Response;

/**
 * Агрегированная статистика
 *
 * @package Coolycow\Dadata\Response
 */
class StatisticServices
{

    /**
     * Поиск дублей.
     *
     * @var int
     */
    public $merging = 0;

    /**
     * Подсказки.
     *
     * @var int
     */
    public $suggestions = 0;

    /**
     * Стандартизация.
     *
     * @var int
     */
    public $clean = 0;
}