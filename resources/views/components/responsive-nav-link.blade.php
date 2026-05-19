@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-4 border-teal-600 bg-teal-50 py-2 ps-3 pe-4 text-start text-base font-bold text-teal-800 transition duration-150 ease-in-out focus:outline-none focus:border-teal-700 focus:bg-teal-100'
            : 'block w-full border-l-4 border-transparent py-2 ps-3 pe-4 text-start text-base font-semibold text-slate-600 transition duration-150 ease-in-out hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:border-slate-300 focus:bg-slate-50 focus:text-slate-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
