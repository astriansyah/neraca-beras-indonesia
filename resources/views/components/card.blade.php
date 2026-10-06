@props(['padding' => 'p-5 sm:p-6', 'as' => 'div'])
<{{ $as }} {{ $attributes->merge(['class' => "rounded-2xl bg-white ring-1 ring-slate-100 shadow-lg shadow-slate-200/60 hover:shadow-xl transition dark:bg-slate-900 dark:ring-slate-800 dark:shadow-black/30 {$padding}"]) }}>
    {{ $slot }}
</{{ $as }}>
