<?php

declare(strict_types=1);

namespace Coolycow\Dadata\Tests;

use Coolycow\Dadata\ClientClean;
use Coolycow\Dadata\ClientSuggest;
use Coolycow\Dadata\DadataServiceProvider;
use Orchestra\Testbench\TestCase;

final class ServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [DadataServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('dadata.token', 'env-token');
        $app['config']->set('dadata.secret', 'env-secret');
    }

    public function testBindingsAreSingletons(): void
    {
        $suggestA = $this->app->make('dadata_suggest');
        $suggestB = $this->app->make('dadata_suggest');
        $cleanA = $this->app->make('dadata_clean');
        $cleanB = $this->app->make('dadata_clean');

        self::assertInstanceOf(ClientSuggest::class, $suggestA);
        self::assertInstanceOf(ClientClean::class, $cleanA);
        self::assertSame($suggestA, $suggestB);
        self::assertSame($cleanA, $cleanB);
    }

    public function testClassAliasesResolve(): void
    {
        self::assertInstanceOf(ClientSuggest::class, $this->app->make(ClientSuggest::class));
        self::assertInstanceOf(ClientClean::class, $this->app->make(ClientClean::class));
    }
}
