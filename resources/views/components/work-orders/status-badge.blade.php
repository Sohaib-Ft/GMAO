@props(['status'])

@php
    $classes = [
        'nouvelle' => 'bg-blue-50 text-blue-500 border-blue-100',
        'en_attente' => 'bg-amber-50 text-amber-600 border-amber-100',
        'affectee' => 'bg-indigo-50 text-indigo-500 border-indigo-100',
        'en_cours' => 'bg-blue-100 text-blue-700 border-blue-200',
        'terminee' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
        'annulee' => 'bg-gray-50 text-gray-500 border-gray-100',
    ];

    $labels = [
        'nouvelle' => 'Nouvelle',
        'en_attente' => 'En attente',
        'affectee' => 'Affectée',
        'en_cours' => 'En cours',
        'terminee' => 'Terminée',
        'annulee' => 'Annulée',
    ];

    $icons = [
        'nouvelle' => 'bx-plus-circle',
        'en_attente' => 'bx-time-five',
        'affectee' => 'bx-user-check',
        'en_cours' => 'bx-play-circle',
        'terminee' => 'bx-check-double',
        'annulee' => 'bx-x-circle',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold border ' . ($classes[$status] ?? 'bg-gray-50 text-gray-500 border-gray-100')]) }}>
    <i class='bx {{ $icons[$status] ?? 'bx-help-circle' }} mr-1.5 text-sm'></i>
    {{ $labels[$status] ?? ucfirst(str_replace('_', ' ', $status)) }}
</span>
