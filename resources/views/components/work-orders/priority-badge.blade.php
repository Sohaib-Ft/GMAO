@props(['priority'])

@php
    $classes = [
        'basse' => 'bg-gray-100 text-gray-500',
        'normale' => 'bg-blue-100 text-blue-600',
        'haute' => 'bg-orange-100 text-orange-600 border border-orange-200',
        'urgente' => 'bg-red-500 text-white animate-pulse shadow-sm shadow-red-200'
    ];

    $priorityLabel = [
        'basse' => 'Basse',
        'normale' => 'Normale',
        'haute' => 'Haute',
        'urgente' => 'Urgente'
    ];
@endphp

<span {{ $attributes->merge(['class' => 'px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest inline-flex items-center ' . ($classes[$priority] ?? 'bg-gray-100 text-gray-600')]) }}>
    @if($priority === 'urgente')
        <i class='bx bxs-error mr-1 text-xs'></i>
    @endif
    {{ $priorityLabel[$priority] ?? ucfirst($priority) }}
</span>
