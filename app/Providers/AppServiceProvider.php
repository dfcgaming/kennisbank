<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (parse_url(config('app.url'), PHP_URL_SCHEME) === 'https') {
            URL::forceScheme('https');
        }

        if (app()->environment('test')) {
            $this->ensureTestStoresAreIsolated();
        }
    }

    private function ensureTestStoresAreIsolated(): void
    {
        $productionDatabase = $this->storeTarget(config('database.production_url'), 5432);
        $testDatabase = $this->storeTarget(config('database.test_url'), 5432);
        $productionRedis = $this->storeTarget(config('database.production_redis_url'), 6379);
        $testRedis = $this->storeTarget(config('database.test_redis_url'), 6379);

        if (! $testDatabase || ! $testRedis) {
            throw new RuntimeException('APP_ENV=test requires TEST_DB_URL and TEST_REDIS_URL.');
        }

        if (! $productionDatabase || ! $productionRedis) {
            throw new RuntimeException('APP_ENV=test requires the production DB_URL and REDIS_URL to verify isolation.');
        }

        if ($testDatabase === $productionDatabase || $testRedis === $productionRedis) {
            throw new RuntimeException('Test database and Redis stores must be separate from production stores.');
        }
    }

    private function storeTarget(?string $url, int $defaultPort): ?string
    {
        if (! $url) {
            return null;
        }

        $parts = parse_url($url);

        if (! is_array($parts) || empty($parts['host'])) {
            return null;
        }

        return strtolower($parts['host']).':'.($parts['port'] ?? $defaultPort).'/'.ltrim($parts['path'] ?? '', '/');
    }
}
