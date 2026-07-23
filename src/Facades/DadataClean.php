<?php

declare(strict_types=1);

namespace Coolycow\Dadata\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Coolycow\Dadata\Response\Address cleanAddress(string $address)
 * @method static \Coolycow\Dadata\Response\Phone cleanPhone(string $phone)
 * @method static \Coolycow\Dadata\Response\Passport cleanPassport(string $passport)
 * @method static \Coolycow\Dadata\Response\Name cleanName(string $name)
 * @method static \Coolycow\Dadata\Response\Email cleanEmail(string $email)
 * @method static \Coolycow\Dadata\Response\Date cleanDate(string $date)
 * @method static \Coolycow\Dadata\Response\Vehicle cleanVehicle(string $vehicle)
 * @method static float getBalance()
 * @method static \Coolycow\Dadata\Response\Statistics getStatistics(string|\DateTimeInterface|null $date = null)
 * @method static \Coolycow\Dadata\Response\Address|null detectAddressByIp(string $ip)
 *
 * @see \Coolycow\Dadata\ClientClean
 */
class DadataClean extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'dadata_clean';
    }
}
