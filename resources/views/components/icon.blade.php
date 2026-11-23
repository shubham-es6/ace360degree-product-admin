{{--
    Small inline SVG icons drawn with currentColor, so they take the text colour.
    Usage: <x-icon name="eye" class="size-4 text-muted" />
--}}
@props(['name'])

<svg {{ $attributes->merge(['class' => 'shrink-0']) }} viewBox="0 0 20 20" fill="none" stroke="currentColor"
     stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('chevron-down')
            <path d="M5.5 8l4.5 4.5L14.5 8" />
            @break
        @case('trend-up')
            <path d="M2.5 14l5-5 3.5 3.5L17.5 6" /><path d="M12.5 6h5v5" />
            @break
        @case('warning')
            <path d="M10 3L18 17H2L10 3z" /><path d="M10 8.5v3.5" /><path d="M10 14.6v.1" />
            @break
        @case('ban')
            <circle cx="10" cy="10" r="7" /><path d="M5 5l10 10" />
            @break
        @case('eye')
            <path d="M1.7 10S4.7 4.2 10 4.2 18.3 10 18.3 10 15.3 15.8 10 15.8 1.7 10 1.7 10z" /><circle cx="10" cy="10" r="2.5" />
            @break
        @case('pencil')
            <path d="M13.6 3.4l3 3L7 16H4v-3l9.6-9.6z" /><path d="M11.6 5.4l3 3" />
            @break
        @case('trash')
            <path d="M3.5 5.5h13" /><path d="M8 5.5V3.5h4v2" /><path d="M5 5.5l.8 11h8.4l.8-11" /><path d="M8.3 8.5v5.5M11.7 8.5v5.5" />
            @break
        @case('image')
            <rect x="3" y="3.5" width="14" height="13" rx="2" /><circle cx="7.5" cy="8" r="1.5" /><path d="M17 13l-4-4-8 7.5" />
            @break
        @case('reset')
            <path d="M3.5 10a6.5 6.5 0 1 0 1.9-4.6L3.5 7.3" /><path d="M3.5 3.5v3.8h3.8" />
            @break
        @case('search-off')
            <circle cx="8.5" cy="8.5" r="5.5" /><path d="M17 17l-4.3-4.3" /><path d="M6.5 6.5l4 4M10.5 6.5l-4 4" />
            @break
        @case('box')
            <path d="M2.5 6.5L10 3l7.5 3.5v7L10 17l-7.5-3.5v-7z" /><path d="M2.5 6.5L10 10l7.5-3.5M10 10v7" />
            @break
    @endswitch
</svg>
