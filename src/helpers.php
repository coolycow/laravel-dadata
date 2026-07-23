<?php

declare(strict_types=1);

if (!function_exists('config_path')) {
    /**
     * Get the configuration path (Lumen compatibility).
     */
    function config_path(string $path = ''): string
    {
        return app()->basePath() . '/config' . ($path !== '' ? '/' . $path : $path);
    }
}
