<?php

namespace Coolycow\Dadata\Facades;

class DadataSuggest extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'dadata_suggest';
    }
}
