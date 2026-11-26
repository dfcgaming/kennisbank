@unless (app()->environment('production'))
    <nav aria-label="Application environment" class="flex items-center gap-2 px-3">
        <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ app()->environment() }}</span>
        <a href="{{ rtrim(config('app.url'), '/') }}/it"
           @if (app()->environment('test')) aria-current="page" @endif
           class="rounded-lg px-3 py-2 text-sm font-semibold {{ app()->environment('test') ? 'bg-primary-50 text-primary-700 dark:bg-primary-400/10 dark:text-primary-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
            Test
        </a>
        <a href="https://kennisbank.sitenomads.nl/it"
           class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5">
            Production
        </a>
    </nav>
@endunless
