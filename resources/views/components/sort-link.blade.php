<a href="{{ $url() }}"
    class="inline-flex items-center gap-1 {{ $isActive() ? 'text-amber-700' : 'text-gray-700 hover:text-gray-900' }}">
    {{ $label }}
    <span class="text-[10px] leading-none">
        @if ($isActive())
            {{ request()->query('direction') === 'asc' ? '▲' : '▼' }}
        @else
            <span class="text-gray-300">▲</span>
        @endif
    </span>
</a>
