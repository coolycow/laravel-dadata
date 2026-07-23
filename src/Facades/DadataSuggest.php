<?php

declare(strict_types=1);

namespace Coolycow\Dadata\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static list<array<string, mixed>> suggest(string $type, array<string, mixed> $fields)
 * @method static list<array<string, mixed>> partyById(string $id, array<string, mixed> $params = [])
 * @method static list<array<string, mixed>> suggestByURL(string $url, string $type, array<string, mixed> $fields)
 *
 * @see \Coolycow\Dadata\ClientSuggest
 */
class DadataSuggest extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'dadata_suggest';
    }
}
