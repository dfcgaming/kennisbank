@php
    $currentEnvironment = app()->environment();
    $testUrl = config('app.test_url') ?: ($currentEnvironment === 'test' ? config('app.url') : null);
    $productionUrl = config('app.production_url', 'https://kennisbank.sitenomads.nl');
@endphp

<nav aria-label="Application environment" class="flex items-center gap-1 rounded-xl border border-gray-200 bg-white/90 p-1 shadow-sm dark:border-white/10 dark:bg-gray-900/90">
    <span class="px-2 text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $currentEnvironment }}</span>
    <a href="{{ $testUrl ? rtrim($testUrl, '/') . '/it' : '#' }}"
       @if ($currentEnvironment === 'test') aria-current="page" @endif
       @if (! $testUrl) aria-disabled="true" tabindex="-1" title="Set TEST_APP_URL to enable the test environment link" @endif
       class="rounded-lg px-3 py-2 text-sm font-semibold {{ $currentEnvironment === 'test' ? 'bg-primary-50 text-primary-700 dark:bg-primary-400/10 dark:text-primary-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }} {{ ! $testUrl ? 'pointer-events-none opacity-50' : '' }}">
        Test
    </a>
    <a href="{{ rtrim($productionUrl, '/') }}/it"
       @if ($currentEnvironment === 'production') aria-current="page" @endif
       class="rounded-lg px-3 py-2 text-sm font-semibold {{ $currentEnvironment === 'production' ? 'bg-primary-50 text-primary-700 dark:bg-primary-400/10 dark:text-primary-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
        Production
    </a>
</nav>
