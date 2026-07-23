<?php

declare(strict_types=1);

namespace Coolycow\Dadata;

use Illuminate\Support\ServiceProvider;

class DadataServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $configPath = __DIR__ . '/../config/dadata.php';
        $publishPath = function_exists('config_path')
            ? config_path('dadata.php')
            : $this->app->basePath('config/dadata.php');

        $this->publishes([$configPath => $publishPath], 'config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/dadata.php', 'dadata');

        $this->app->singleton('dadata_suggest', function ($app) {
            $config = $app['config']->get('dadata', []);

            return new ClientSuggest(
                token: (string) ($config['token'] ?? ''),
            );
        });

        $this->app->singleton('dadata_clean', function ($app) {
            $config = $app['config']->get('dadata', []);

            return new ClientClean(
                token: (string) ($config['token'] ?? ''),
                secret: isset($config['secret']) ? (string) $config['secret'] : null,
            );
        });

        $this->app->alias('dadata_suggest', ClientSuggest::class);
        $this->app->alias('dadata_clean', ClientClean::class);
    }

    /**
     * @return list<string>
     */
    public function provides(): array
    {
        return [
            'dadata_suggest',
            'dadata_clean',
            ClientSuggest::class,
            ClientClean::class,
        ];
    }
}
