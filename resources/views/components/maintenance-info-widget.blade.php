@props([
    'maintenance' => [],
    'onlyNextWeek' => false,
])

@php
    $now = now();
    $dates = [];
    
    // Grouper les maintenances par date
    foreach ($maintenance as $item) {
        $dateKey = $item['date']->format('Y-m-d');
        if (!isset($dates[$dateKey])) {
            $dates[$dateKey] = [];
        }
        $dates[$dateKey][] = $item;
    }
    
    // Filtrer pour prochaine semaine si demandé
    if ($onlyNextWeek) {
        $weekEnd = $now->copy()->addDays(7);
        $dates = array_filter($dates, function($k) use ($now, $weekEnd) {
            $date = new \DateTime($k);
            return $date >= $now && $date <= $weekEnd;
        }, ARRAY_FILTER_USE_KEY);
    }
    
    // Trier par date
    ksort($dates);
@endphp

<div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
    {{-- Header Animé --}}
    <div class="bg-gradient-to-r from-indigo-600 to-blue-600 text-white px-6 py-4 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-indigo-400 opacity-10 rounded-full -mr-20 -mt-20"></div>
        <h3 class="text-lg font-bold flex items-center relative z-10">
            <i class='bx bx-calendar-check mr-2 text-2xl animate-pulse'></i>
            Maintenance Planifiée
        </h3>
        <p class="text-sm text-indigo-100 mt-1">
            💡 Système informatif - Vous êtes notifié des dates de maintenance
        </p>
    </div>

    {{-- Content --}}
    <div class="max-h-96 overflow-y-auto">
        @forelse($dates as $dateStr => $items)
            @php
                $date = new \DateTime($dateStr);
                $isToday = $date->format('Y-m-d') === now()->format('Y-m-d');
                $isTomorrow = $date->format('Y-m-d') === now()->addDay()->format('Y-m-d');
                $daysAway = (int)(($date->getTimestamp() - now()->getTimestamp()) / 86400);
            @endphp
            
            {{-- Ligne de date avec maintenance --}}
            <div class="border-b border-gray-100 last:border-0 hover:bg-indigo-50 transition py-4 px-6">
                {{-- Date Row --}}
                <div class="flex items-center gap-4 mb-3">
                    {{-- Date Box --}}
                    <div class="w-16 h-16 rounded-xl flex flex-col items-center justify-center flex-shrink-0
                        @if($isToday)
                            bg-green-100
                        @elseif($isTomorrow)
                            bg-orange-100
                        @else
                            bg-indigo-100
                        @endif">
                        <p class="text-xs font-bold uppercase
                            @if($isToday)
                                text-green-600
                            @elseif($isTomorrow)
                                text-orange-600
                            @else
                                text-indigo-600
                            @endif">
                            {{ $date->format('M') }}
                        </p>
                        <p class="text-2xl font-bold
                            @if($isToday)
                                text-green-600
                            @elseif($isTomorrow)
                                text-orange-600
                            @else
                                text-indigo-600
                            @endif">
                            {{ $date->format('d') }}
                        </p>
                        <p class="text-xs
                            @if($isToday)
                                text-green-500
                            @elseif($isTomorrow)
                                text-orange-500
                            @else
                                text-indigo-500
                            @endif">
                            {{ $date->format('D') }}
                        </p>
                    </div>

                    {{-- Date Info --}}
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-base font-bold text-gray-800">
                                {{ $date->format('l') }} 
                                <span class="text-gray-600">{{ $date->format('j F Y') }}</span>
                            </p>
                            @if($isToday)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                    bg-green-100 text-green-700 gap-1">
                                    <i class='bx bx-check-circle text-sm'></i>
                                    Aujourd'hui
                                </span>
                            @elseif($isTomorrow)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                    bg-orange-100 text-orange-700 gap-1">
                                    <i class='bx bx-time text-sm'></i>
                                    Demain
                                </span>
                            @elseif($daysAway < 0)
                                <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">
                                    Passée
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                    bg-blue-100 text-blue-700 gap-1">
                                    <i class='bx bx-calendar-event text-sm'></i>
                                    {{ $daysAway }} jour@if($daysAway !== 1)s@endif
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mt-2 font-medium">
                            📋 {{ count($items) }} intervention@if(count($items) > 1)s@endif prévue@if(count($items) > 1)s@endif
                        </p>
                    </div>
                </div>

                {{-- Maintenance Items --}}
                <div class="space-y-2 ml-20">
                    @foreach($items as $idx => $item)
                        <div class="rounded-lg p-3 border-2
                            @if($item['plan']->type === 'preventive')
                                bg-green-50 border-green-200
                            @else
                                bg-red-50 border-red-200
                            @endif
                            hover:shadow-md transition">
                            
                            <div class="flex items-start justify-between gap-3">
                                {{-- Maintenance Details --}}
                                <div class="flex-1 min-w-0">
                                    {{-- Équipement --}}
                                    <p class="font-bold text-gray-800 truncate">
                                        <i class='bx bx-wrench
                                            @if($item['plan']->type === 'preventive')
                                                text-green-600
                                            @else
                                                text-red-600
                                            @endif
                                            mr-1'></i>
                                        {{ $item['plan']->equipement->nom }}
                                    </p>

                                    {{-- Code équipement --}}
                                    <p class="text-xs text-gray-600 font-mono mt-1">
                                        ID: {{ $item['plan']->equipement->code }}
                                    </p>

                                    {{-- Row 2: Time et Type --}}
                                    <div class="flex items-center gap-3 mt-2 flex-wrap">
                                        <span class="text-sm font-medium
                                            @if($item['plan']->type === 'preventive')
                                                text-green-700
                                            @else
                                                text-red-700
                                            @endif">
                                            <i class='bx bx-time text-base'></i>
                                            {{ $item['date']->format('H:i') }}
                                        </span>

                                        <span class="text-xs px-2 py-1 rounded-full font-bold
                                            @if($item['plan']->type === 'preventive')
                                                bg-green-100 text-green-700
                                            @else
                                                bg-red-100 text-red-700
                                            @endif">
                                            @if($item['plan']->type === 'preventive')
                                                🛡️ Préventive
                                            @else
                                                ⚠️ Corrective
                                            @endif
                                        </span>
                                    </div>

                                    {{-- Description --}}
                                    @if($item['plan']->description)
                                        <p class="text-sm text-gray-700 mt-2 italic line-clamp-2">
                                            "{{ $item['plan']->description }}"
                                        </p>
                                    @endif

                                    {{-- Localisation --}}
                                    <p class="text-xs text-gray-600 mt-2">
                                        <i class='bx bx-map text-gray-500'></i>
                                        {{ $item['plan']->equipement->localisation->nom ?? 'Localisation inconnue' }}
                                    </p>
                                </div>

                                {{-- Status Dot --}}
                                @if($isToday)
                                    <div class="ml-2 flex-shrink-0 mt-1">
                                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
        @empty
            {{-- Empty State --}}
            <div class="py-16 text-center px-6">
                <i class='bx bx-calendar-x text-7xl text-gray-300 mb-4'></i>
                <p class="text-gray-700 font-bold text-lg">Aucune maintenance prévue</p>
                <p class="text-gray-600 text-sm mt-2">
                    @if($onlyNextWeek)
                        pour la semaine à venir
                    @else
                        pour les 30 prochains jours
                    @endif
                </p>
                <p class="text-gray-500 text-xs mt-4">
                    L'administrateur ajoutera les maintenances planifiées ici
                </p>
            </div>
        @endforelse
    </div>

    {{-- Footer --}}
    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 px-6 py-3 border-t border-gray-200 flex items-center justify-between">
        <p class="text-xs text-gray-700 font-medium">
            ✓ Vous êtes informé quand une nouvelle maintenance est ajoutée
        </p>
        <a href="{{ route('maintenance-calendar') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-bold flex items-center gap-1 transition">
            Voir le calendrier 
            <i class='bx bx-calendar'></i>
        </a>
    </div>
</div>

