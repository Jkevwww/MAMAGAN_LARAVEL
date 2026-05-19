@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center border-b-2 border-teal-600 px-1 pt-1 text-sm font-bold leading-5 text-slate-950 transition duration-150 ease-in-out focus:outline-none focus:border-teal-700'
            : 'inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-semibold leading-5 text-slate-500 transition duration-150 ease-in-out hover:border-slate-300 hover:text-slate-900 focus:outline-none focus:border-slate-300 focus:text-slate-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
