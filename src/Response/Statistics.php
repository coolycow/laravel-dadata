<?php

declare(strict_types=1);

namespace Coolycow\Dadata\Response;

use DateTimeInterface;

/**
 * Usage statistics.
 *
 * Агрегированная статистика за конкретный день по каждому из сервисов:
 * стандартизация - clean;
 * подсказки - suggestions;
 * поиск дублей - merging.
 *
 * @link https://dadata.ru/api/stat/
 */
class Statistics extends AbstractResponse
{
    /**
     * @var DateTimeInterface|null Дата отчета.
     */
    public ?DateTimeInterface $date = null;

    /**
     * @var StatisticServices|null Услуги.
     */
    public ?StatisticServices $services = null;
}
