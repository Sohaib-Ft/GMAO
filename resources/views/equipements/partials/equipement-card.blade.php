{{-- Carte d'affichage d'un équipement avec son code unique --}}

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
    {{-- Header avec code et statut --}}
    <div class="flex items-start justify-between mb-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $equipement->nom }}
            </h3>
            <div class="mt-2 flex items-center space-x-3">
                {{-- Code d'équipement (Asset Code) --}}
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-mono font-semibold bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                    </svg>
                    {{ $equipement->code }}
                </span>

                {{-- Statut --}}
                @php
                    $statusColors = [
                        'actif' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                        'inactif' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
                        'maintenance' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                        'panne' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                    ];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $statusColors[$equipement->statut] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($equipement->statut) }}
                </span>
            </div>
        </div>

        {{-- Boutons d'action --}}
        <div class="flex space-x-2">
            <a href="{{ route('equipements.edit', $equipement) }}" 
               class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
        </div>
    </div>

    {{-- Informations de l'équipement --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
        {{-- Catégorie et Type --}}
        <div>
            <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                Classification
            </h4>
            <dl class="space-y-1">
                @if($equipement->categorie)
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Catégorie</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $equipement->categorie }}</dd>
                </div>
                @endif
                @if($equipement->type_equipement)
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Type</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $equipement->type_equipement }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Localisation --}}
        <div>
            <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                Localisation
            </h4>
            <dl class="space-y-1">
                @if($equipement->departement)
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Département</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $equipement->departement }}</dd>
                </div>
                @endif
                @if($equipement->site)
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Site</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $equipement->site }}</dd>
                </div>
                @endif
                @if($equipement->localisation)
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Emplacement précis</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $equipement->site }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Caractéristiques --}}
        <div>
            <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                Caractéristiques
            </h4>
            <dl class="space-y-1">
                @if($equipement->marque)
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Marque</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $equipement->marque }}</dd>
                </div>
                @endif
                @if($equipement->modele)
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Modèle</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $equipement->modele }}</dd>
                </div>
                @endif
                @if($equipement->numero_serie)
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">N° Série</dt>
                    <dd class="text-sm font-mono text-gray-900 dark:text-white">{{ $equipement->numero_serie }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Dates --}}
        @if($equipement->annee_acquisition || $equipement->date_installation || $equipement->date_fin_garantie)
        <div>
            <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                Dates importantes
            </h4>
            <dl class="space-y-1">
                @if($equipement->annee_acquisition)
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Année d'acquisition</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $equipement->annee_acquisition }}</dd>
                </div>
                @endif
                @if($equipement->date_installation)
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Date d'installation</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $equipement->date_installation->format('d/m/Y') }}</dd>
                </div>
                @endif
                @if($equipement->date_fin_garantie)
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Fin de garantie</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $equipement->date_fin_garantie->format('d/m/Y') }}</dd>
                </div>
                @endif
            </dl>
        </div>
        @endif

        {{-- Responsable --}}
        @if($equipement->responsable)
        <div>
            <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                Responsable
            </h4>
            <dl class="space-y-1">
                <div>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $equipement->responsable->name }}
                    </dd>
                </div>
            </dl>
        </div>
        @endif

        {{-- Statistiques --}}
        @if($equipement->compteur_heures > 0)
        <div>
            <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                Statistiques
            </h4>
            <dl class="space-y-1">
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Heures de fonctionnement</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($equipement->compteur_heures) }} h</dd>
                </div>
            </dl>
        </div>
        @endif
    </div>

    {{-- Notes --}}
    @if($equipement->notes)
    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
        <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
            Notes
        </h4>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $equipement->notes }}</p>
    </div>
    @endif
</div>
