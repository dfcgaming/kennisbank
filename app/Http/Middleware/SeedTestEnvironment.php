<?php

namespace App\Http\Middleware;

use Closure;
use Database\Seeders\TestEnvironmentSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SeedTestEnvironment
{
    private static bool $seeded = false;

    public function handle(Request $request, Closure $next): Response
    {
        if (! self::$seeded && app()->environment('test') && Auth::check()) {
            app(TestEnvironmentSeeder::class)->run();
            self::$seeded = true;
        }

        return $next($request);
    }
}
