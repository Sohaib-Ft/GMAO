@extends('layouts.app')

@section('title', 'Détails Équipement')
@section('header', 'Fiche technique')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header Card -->
    <div class="bg-white p-6 rounded-3xl shadow-xl border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="flex items-center gap-5">
            <div class="h-20 w-20 rounded-2xl bg-gray-100 border border-gray-200 overflow-hidden flex-shrink-0">
                @if($equipment->image_path)
                    <img src="{{ Storage::url($equipment->image_path) }}" class="h-full w-full object-cover">
                @else
                    <div class="h-full w-full flex items-center justify-center text-gray-400">
                        <i class='bx bx-image text-4xl'></i>
                    </div>
                @endif
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $equipment->nom }}</h1>
                <div class="flex items-center gap-3 mt-2">
                    <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-lg text-sm font-mono font-bold border border-blue-100">
                        {{ $equipment->code }}
                    </span>
                    @php
                        $statusClasses = [
                            'actif' => 'bg-green-100 text-green-700 border-green-200',
                            'maintenance' => 'bg-orange-100 text-orange-700 border-orange-200',
                            'panne' => 'bg-red-100 text-red-700 border-red-200',
                            'inactif' => 'bg-gray-100 text-gray-700 border-gray-200',
                        ];
                    @endphp
                    <span class="px-3 py-1 rounded-lg text-sm font-bold border {{ $statusClasses[$equipment->statut] ?? 'bg-gray-100' }}">
                        {{ ucfirst($equipment->statut) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
             <a href="{{ route('technician.equipments.index') }}" class="flex items-center px-5 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition font-bold">
                <i class='bx bx-arrow-back mr-2'></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Tech Specs -->
            <div class="bg-white p-6 rounded-3xl shadow-xl border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class='bx bx-chip mr-2 text-blue-500'></i> Spécifications Techniques
                </h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                    <div>
                        <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider">Marque</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $equipment->marque ?: '-' }}</dd>
                    </div>
                     <div>
                        <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider">Modèle</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $equipment->modele ?: '-' }}</dd>
                    </div>
                     <div>
                        <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider">Numéro de Série</dt>
                        <dd class="mt-1 text-gray-900 font-mono">{{ $equipment->numero_serie ?: '-' }}</dd>
                    </div>
                     <div>
                        <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider">Année</dt>
                        <dd class="mt-1 text-gray-900">{{ $equipment->annee_fabrication ?: '-' }}</dd>
                    </div>
                    <div class="md:col-span-2 mt-2 pt-4 border-t border-gray-50">
                        <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Notes</dt>
                        <dd class="text-gray-600 text-sm bg-gray-50 p-4 rounded-xl leading-relaxed">
                            {{ $equipment->notes ?: 'Aucune note enregistrée.' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Location -->
            <div class="bg-white p-6 rounded-3xl shadow-xl border border-gray-100">
                 <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class='bx bx-map-alt mr-2 text-blue-500'></i> Localisation
                </h3>
                
                <div class="mb-6">
                    <div class="flex items-center text-gray-900 font-medium mb-1">
                        <i class='bx bx-building text-gray-400 mr-2'></i>
                        {{ $equipment->site ?? 'Non défini' }}
                    </div>
                    <p class="text-xs text-gray-400 ml-6">Emplacement physique</p>
                </div>
            </div>

            <!-- Docs -->
            <div class="bg-white p-6 rounded-3xl shadow-xl border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class='bx bx-folder mr-2 text-blue-500'></i> Documents
                </h3>
                @if($equipment->manuel_path)
                    <a href="{{ Storage::url($equipment->manuel_path) }}" target="_blank" class="flex items-center p-3 rounded-xl bg-red-50 hover:bg-red-100 transition border border-red-100 group">
                        <i class='bx bxs-file-pdf text-3xl text-red-500 mr-3 group-hover:scale-110 transition-transform'></i>
                        <div>
                            <div class="text-sm font-bold text-red-900">Manuel Technique</div>
                            <div class="text-xs text-red-600">PDF Document</div>
                        </div>
                    </a>
                @else
                    <div class="text-center py-6 text-gray-400 text-sm">
                        <i class='bx bx-file-blank text-3xl mb-2 block opacity-50'></i>
                        Aucun document
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
