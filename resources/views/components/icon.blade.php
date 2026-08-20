@props(['name'])

@php
$icons = [
    'home' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 11.5 12 4l9 7.5" /><path stroke-linecap="round" stroke-linejoin="round" d="M5.5 10v9a1 1 0 0 0 1 1h3v-6h5v6h3a1 1 0 0 0 1-1v-9" />',
    'grid' => '<rect x="3.5" y="3.5" width="7" height="7" rx="1.2" /><rect x="13.5" y="3.5" width="7" height="7" rx="1.2" /><rect x="3.5" y="13.5" width="7" height="7" rx="1.2" /><rect x="13.5" y="13.5" width="7" height="7" rx="1.2" />',
    'users' => '<circle cx="9" cy="8" r="3" /><path stroke-linecap="round" stroke-linejoin="round" d="M3.5 20a5.5 5.5 0 0 1 11 0" /><circle cx="17.3" cy="9.5" r="2.3" /><path stroke-linecap="round" stroke-linejoin="round" d="M14.8 20a4.3 4.3 0 0 1 7.7-2.6" />',
    'user-plus' => '<circle cx="9" cy="8" r="3" /><path stroke-linecap="round" stroke-linejoin="round" d="M3 20a6 6 0 0 1 12 0" /><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v6M16 10.5h6" />',
    'book-open' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.5c-1.8-1.3-4.2-2-6.5-2-.6 0-1 .4-1 1v11c0 .6.4 1 1 1 2.3 0 4.7.7 6.5 2 1.8-1.3 4.2-2 6.5-2 .6 0 1-.4 1-1v-11c0-.6-.4-1-1-1-2.3 0-4.7.7-6.5 2Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.5v13" />',
    'tag' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11 3.5H5a1.5 1.5 0 0 0-1.5 1.5v6L12.5 20l7-7L11 3.5Z" /><circle cx="8" cy="8" r="1.3" />',
    'layers' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3.5 3.5 8 12 12.5 20.5 8 12 3.5Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M3.5 12 12 16.5 20.5 12" /><path stroke-linecap="round" stroke-linejoin="round" d="M3.5 16 12 20.5 20.5 16" />',
    'cog' => '<circle cx="12" cy="12" r="3.2" /><path stroke-linecap="round" d="M12 3.5v2.3M12 18.2v2.3M20.5 12h-2.3M5.8 12H3.5M17.7 6.3l-1.6 1.6M7.9 16.1l-1.6 1.6M17.7 17.7l-1.6-1.6M7.9 7.9 6.3 6.3" />',
    'shield-check' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3.5 19 6v6c0 4.4-2.9 7.4-7 8.5-4.1-1.1-7-4.1-7-8.5V6l7-2.5Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4.2" />',
    'chart-bar' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 20V10M10 20V4M16 20v-7M4 20h16" />',
    'logout' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 20H6a1.5 1.5 0 0 1-1.5-1.5v-13A1.5 1.5 0 0 1 6 4h3" /><path stroke-linecap="round" stroke-linejoin="round" d="M14.5 8.5 19 12l-4.5 3.5M19 12H9" />',
    'search' => '<circle cx="10.5" cy="10.5" r="6.5" /><path stroke-linecap="round" d="m20 20-4.5-4.5" />',
    'plus' => '<path stroke-linecap="round" d="M12 5v14M5 12h14" />',
    'pencil' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 20h4L18.5 9.5a2 2 0 0 0-3-3L4.5 17v3" /><path stroke-linecap="round" stroke-linejoin="round" d="m14.3 7.3 3 3" />',
    'trash' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.5h15M9.5 6.5V4.8c0-.7.6-1.3 1.3-1.3h2.4c.7 0 1.3.6 1.3 1.3v1.7M18 6.5l-.8 12.4a1.6 1.6 0 0 1-1.6 1.6H8.4a1.6 1.6 0 0 1-1.6-1.6L6 6.5" /><path stroke-linecap="round" d="M10 10.5v6M14 10.5v6" />',
    'check-circle' => '<circle cx="12" cy="12" r="8.5" /><path stroke-linecap="round" stroke-linejoin="round" d="m8.5 12.3 2.4 2.4 4.6-5" />',
    'x-circle' => '<circle cx="12" cy="12" r="8.5" /><path stroke-linecap="round" d="m9.3 9.3 5.4 5.4M14.7 9.3l-5.4 5.4" />',
    'download' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3.5v11M8 11l4 4 4-4" /><path stroke-linecap="round" d="M4.5 17v2a1.5 1.5 0 0 0 1.5 1.5h12a1.5 1.5 0 0 0 1.5-1.5v-2" />',
    'clipboard-list' => '<rect x="5.5" y="5" width="13" height="15.5" rx="1.5" /><rect x="9" y="3.5" width="6" height="3" rx="1" /><path stroke-linecap="round" d="M8.5 11h7M8.5 14.5h7M8.5 18h4.5" />',
    'menu' => '<path stroke-linecap="round" d="M4 6.5h16M4 12h16M4 17.5h16" />',
    'x-mark' => '<path stroke-linecap="round" d="m6 6 12 12M18 6 6 18" />',
    'chevron-down' => '<path stroke-linecap="round" stroke-linejoin="round" d="m5.5 8.5 6.5 7 6.5-7" />',
    'chevron-right' => '<path stroke-linecap="round" stroke-linejoin="round" d="m9 5.5 7 6.5-7 6.5" />',
    'arrow-right' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 12h16M14 6l6 6-6 6" />',
    'academic-cap' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5 2.5 9 12 13.5 21.5 9 12 4.5Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6.5 11v4.5c0 1.4 2.5 2.5 5.5 2.5s5.5-1.1 5.5-2.5V11" /><path stroke-linecap="round" d="M21.5 9v6" />',
    'sparkles' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3.5c.4 2.6 1 4.2 2.1 5.4 1.2 1.1 2.8 1.7 5.4 2.1-2.6.4-4.2 1-5.4 2.1-1.1 1.2-1.7 2.8-2.1 5.4-.4-2.6-1-4.2-2.1-5.4-1.2-1.1-2.8-1.7-5.4-2.1 2.6-.4 4.2-1 5.4-2.1 1.1-1.2 1.7-2.8 2.1-5.4Z" />',
    'lock' => '<rect x="5" y="10.5" width="14" height="9.5" rx="1.5" /><path stroke-linecap="round" stroke-linejoin="round" d="M8 10.5V7.5a4 4 0 0 1 8 0v3" />',
    'bolt' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13 3.5 5.5 13.5H11l-1 7 7.5-10.5H12l1-6.5Z" />',
    'device' => '<rect x="3.5" y="4.5" width="17" height="11" rx="1.5" /><path stroke-linecap="round" d="M9 19.5h6M12 15.5v4" />',
    'globe' => '<circle cx="12" cy="12" r="8.5" /><path stroke-linecap="round" d="M3.5 12h17M12 3.5c2.2 2.2 3.3 5.2 3.3 8.5s-1.1 6.3-3.3 8.5c-2.2-2.2-3.3-5.2-3.3-8.5S9.8 5.7 12 3.5Z" />',
    'building' => '<rect x="5" y="3.5" width="14" height="17" rx="1" /><path stroke-linecap="round" d="M9 7.5h.01M15 7.5h.01M9 11h.01M15 11h.01M9 14.5h.01M15 14.5h.01M10 20.5v-4h4v4" />',
    'clock' => '<circle cx="12" cy="12" r="8.5" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5V12l3 2" />',
    'mail' => '<rect x="3.5" y="5.5" width="17" height="13" rx="1.5" /><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 7 7.5 6 7.5-6" />',
    'filter' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 5.5h16L14 13v6l-4 2v-8L4 5.5Z" />',
];
$path = $icons[$name] ?? $icons['sparkles'];
@endphp

<svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
    {!! $path !!}
</svg>
