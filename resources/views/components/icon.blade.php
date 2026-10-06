@props(['name', 'class' => 'size-5'])
@php
    // Ikon garis (24×24, stroke) — gaya Heroicons/Lucide.
    $paths = [
        'home' => 'M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-9.5Z',
        'sprout' => 'M12 21v-9m0 0c0-3.5-2.5-6-7-6 0 4 2.5 6 7 6Zm0 0c0-4 3-7 8-7 0 4.5-3 7-8 7ZM7 21h10',
        'bowl' => 'M3 12h18a9 9 0 0 1-18 0Zm4-3c0-1.5 1-2.5 2-3m3 3c0-2 1-3.5 2.5-4.5M16 9c0-1 .5-2 1.5-2.5M8 21h8',
        'ship' => 'M3 17l1.5 3h15L21 17M5 17l-1-5h16l-1 5M8 12V7h8v5M12 7V3m-7 18c1 .7 2 .7 3 0s2-.7 3 0 2 .7 3 0 2-.7 3 0',
        'warehouse' => 'M3 21V9l9-6 9 6v12M7 21v-8h10v8M7 17h10',
        'flow' => 'M4 6h5c3 0 3 6 6 6h5M4 18h5c3 0 3-6 6-6M17 9l3 3-3 3',
        'dice' => 'M5 4h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Zm3.5 4.5h.01M15.5 8.5h.01M12 12h.01M8.5 15.5h.01M15.5 15.5h.01',
        'table' => 'M3 5h18v14H3V5Zm0 5h18M3 15h18M9 5v14',
        'book' => 'M4 19.5V5a2 2 0 0 1 2-2h13v16H6a2 2 0 0 0-2 2Zm0 0A2 2 0 0 0 6 22h13M8 7h7M8 11h5',
        'sun' => 'M12 4V2m0 20v-2m8-8h2M2 12h2m13.66-5.66 1.41-1.41M4.93 19.07l1.41-1.41m0-11.32L4.93 4.93m14.14 14.14-1.41-1.41M16 12a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z',
        'moon' => 'M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z',
        'menu' => 'M4 6h16M4 12h16M4 18h16',
        'x' => 'M6 6l12 12M18 6 6 18',
        'chevrons-left' => 'm11 17-5-5 5-5m7 10-5-5 5-5',
        'download' => 'M12 4v11m0 0-4-4m4 4 4-4M4 19h16',
        'info' => 'M12 16v-4m0-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        'eye' => 'M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Zm10 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z',
        'alert' => 'M12 9v4m0 4h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z',
        'external' => 'M14 4h6v6m0-6-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5',
        'search' => 'm21 21-4.3-4.3M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z',
        'sparkles' => 'M12 3l1.8 4.7L18.5 9.5l-4.7 1.8L12 16l-1.8-4.7L5.5 9.5l4.7-1.8L12 3Zm7 12 .9 2.1L22 18l-2.1.9L19 21l-.9-2.1L16 18l2.1-.9L19 15Z',
        'approx' => 'M5 9c2-2 4-2 7 0s5 2 7 0M5 15c2-2 4-2 7 0s5 2 7 0',
        'arrow-up' => 'M12 19V5m0 0-6 6m6-6 6 6',
        'arrow-down' => 'M12 5v14m0 0 6-6m-6 6-6-6',
        'trend' => 'M3 17l6-6 4 4 8-8m0 0h-6m6 0v6',
        'trending-up' => 'M3 17l6-6 4 4 8-8m0 0h-6m6 0v6',
        'trending-down' => 'M3 7l6 6 4-4 8 8m0 0h-6m6 0v-6',
        'chart-bar' => 'M3 3v18h18M7 16h2v-4H7v4Zm4 4h2V8h-2v12Zm4 0h2V4h-2v16Z',
        'scale' => 'M12 3v18M5 21h14M6 7h12M6 7l-3 7a3 3 0 0 0 6 0L6 7Zm12 0-3 7a3 3 0 0 0 6 0l-3-7Z',
        'globe' => 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0c2.5-2.5 3.5-5.5 3.5-9S14.5 5.5 12 3m0 18c-2.5-2.5-3.5-5.5-3.5-9S9.5 5.5 12 3M3.5 9h17M3.5 15h17',
        'shield' => 'M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6l-8-3Zm-3 9 2 2 4-4',
        'external-link' => 'M14 4h6v6m0-6-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5',
        'sliders' => 'M4 21v-7m0-4V3m8 18v-9m0-4V3m8 18v-5m0-4V3M1 14h6m2-6h6m2 8h6',
        'filter' => 'M3 4h18v2l-7 8v5l-4 2v-7L3 6V4Z',
        'check' => 'M20 6 9 17l-5-5',
    ];
@endphp
<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    <path d="{{ $paths[$name] ?? $paths['info'] }}" />
</svg>
