<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel DaData
    |--------------------------------------------------------------------------
    |
    | token  — API-ключ (Authorization: Token …)
    | secret — секретный ключ для API стандартизации (X-Secret)
    |
    */
    'token' => env('DADATA_TOKEN', ''),

    'secret' => env('DADATA_SECRET', ''),
];
