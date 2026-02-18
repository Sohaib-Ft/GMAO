@extends('layouts.app')

@section('title', 'Dashboard Technicien - Maintenances')

@section('content')
<div class="min-h-screen bg-gray-50">
    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        Bienvenue, {{ auth()->user()->name }}
                    </h1>
                    <p class="text-gray-600 mt-1">
                        <i class='bx bx-calendar-check'></i>
                        Tableau de bord des maintenances planifiées
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('maintenance-calendar') }}" 
                        class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition font-medium">
                        <i class='bx bx-calendar mr-2'></i>
                        Calendrier
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Statistics Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- Total Plans --}}
            <div class="bg-white rounded-xl shadow border border-gray-200 p-6 hover:shadow-lg transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Plans Actifs</p>
                        <p class="text-4xl font-bold text-indigo-600 mt-2">{{ $stats['total_plans'] }}</p>
                        <p class="text-xs text-gray-500 mt-2">en cours de maintenance</p>
                    </div>
                    <div class="w-14 h-14 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class='bx bx-wrench text-2xl text-indigo-600'></i>
                    </div>
                </div>
            </div>

            {{-- This Week --}}
            <div class="bg-white rounded-xl shadow border border-gray-200 p-6 hover:shadow-lg transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Cette Semaine</p>
                        <p class="text-4xl font-bold text-orange-600 mt-2">{{ $stats['this_week'] }}</p>
                        <p class="text-xs text-gray-500 mt-2">interventions prévues</p>
                    </div>
                    <div class="w-14 h-14 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class='bx bx-calendar-event text-2xl text-orange-600'></i>
                    </div>
                </div>
            </div>

            {{-- Next 30 Days --}}
            <div class="bg-white rounded-xl shadow border border-gray-200 p-6 hover:shadow-lg transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Prochaines 30 Jours</p>
                        <p class="text-4xl font-bold text-green-600 mt-2">{{ $stats['next_30_days'] }}</p>
                        <p class="text-xs text-gray-500 mt-2">à planifier</p>
                    </div>
                    <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class='bx bx-calendar-month text-2xl text-green-600'></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Widget - Maintenance Calendar View --}}
        <div class="mb-8">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                {{-- Filters --}}
                <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class='bx bx-calendar text-2xl mr-2 text-indigo-600'></i>
                            Calendrier de Maintenance
                        </h3>
                        <div class="flex gap-3">
                            <select id="viewType" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:border-gray-400 transition cursor-pointer">
                                <option value="month">Vue Mois</option>
                                <option value="week">Vue Semaine</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Calendar Container --}}
                <div id="calendarContainer" class="p-6">
                    {{-- Calendrier du mois --}}
                    @php
                        $now = now();
                        $weeks = [];
                        $firstDay = $now->copy()->startOfMonth()->startOfWeek();
                        $lastDay = $now->copy()->endOfMonth()->endOfWeek();
                        
                        $current = $firstDay->copy();
                        while ($current <= $lastDay) {
                            $weeks[] = [
                                'start' => $current->copy(),
                                'days' => array_map(fn($i) => $current->copy()->addDays($i), range(0, 6))
                            ];
                            $current->addWeek();
                        }

                        // Créer la map des maintenances par date
                        $maintenanceMap = [];
                        foreach ($maintenance as $item) {
                            $key = $item['date']->format('Y-m-d');
                            if (!isset($maintenanceMap[$key])) {
                                $maintenanceMap[$key] = [];
                            }
                            $maintenanceMap[$key][] = $item;
                        }
                    @endphp

                    {{-- Navigation --}}
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                        <button class="px-4 py-2 flex items-center gap-2 text-gray-700 hover:bg-gray-100 rounded-lg transition font-medium" onclick="previousMonth()">
                            <i class='bx bx-chevron-left'></i>
                            Précédent
                        </button>
                        
                        <h4 class="text-lg font-bold text-gray-800" id="monthTitle">
                            {{ $now->format('F Y') }}
                        </h4>
                        
                        <button class="px-4 py-2 flex items-center gap-2 text-gray-700 hover:bg-gray-100 rounded-lg transition font-medium" onclick="nextMonth()">
                            Suivant
                            <i class='bx bx-chevron-right'></i>
                        </button>
                    </div>

                    {{-- Calendar Grid --}}
                    <div class="bg-white">
                        {{-- Days Header --}}
                        <div class="grid grid-cols-7 gap-0 mb-2 bg-indigo-50 rounded-t-lg overflow-hidden">
                            @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $day)
                                <div class="p-3 text-center font-bold text-indigo-700 text-sm">{{ $day }}</div>
                            @endforeach
                        </div>

                        {{-- Days Grid --}}
                        @foreach($weeks as $week)
                            <div class="grid grid-cols-7 gap-0 border border-gray-200 rounded-lg overflow-hidden mb-3">
                                @foreach($week['days'] as $day)
                                    @php
                                        $dateKey = $day->format('Y-m-d');
                                        $items = $maintenanceMap[$dateKey] ?? [];
                                        $isToday = $day->format('Y-m-d') === now()->format('Y-m-d');
                                        $isCurrentMonth = $day->month === $now->month;
                                    @endphp
                                    
                                    <div class="aspect-square p-2 border border-gray-100 transition hover:shadow-md cursor-pointer group relative
                                        @if($isToday)
                                            bg-green-50 border-2 border-green-500
                                        @elseif(!$isCurrentMonth)
                                            bg-gray-50
                                        @else
                                            bg-white
                                        @endif
                                        @if(count($items) > 0)
                                            hover:bg-indigo-50
                                        @endif">
                                        
                                        {{-- Day Number --}}
                                        <p class="text-xs font-bold mb-1
                                            @if($isToday)
                                                text-green-700
                                            @elseif(!$isCurrentMonth)
                                                text-gray-400
                                            @else
                                                text-gray-700
                                            @endif">
                                            {{ $day->format('d') }}
                                        </p>

                                        {{-- Maintenance Dots --}}
                                        @if(count($items) > 0)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($items as $item)
                                                    <div class="w-2 h-2 rounded-full
                                                        @if($item['plan']->type === 'preventive')
                                                            bg-green-500
                                                        @else
                                                            bg-red-500
                                                        @endif
                                                        animate-pulse"></div>
                                                @endforeach
                                            </div>

                                            {{-- Tooltip --}}
                                            <div class="hidden group-hover:block absolute left-0 top-full mt-1 z-50 bg-gray-900 text-white text-xs rounded-lg p-2 whitespace-nowrap min-w-max shadow-lg">
                                                {{ count($items) }} intervention@if(count($items) > 1)s@endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        {{-- Legend --}}
                        <div class="flex items-center justify-center gap-6 mt-6 pt-4 border-t border-gray-200 text-sm">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <span class="text-gray-700">Préventive</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                <span class="text-gray-700">Corrective</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 bg-green-500 rounded-full border-2 border-green-700"></div>
                                <span class="text-gray-700">Aujourd'hui</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Next Week Widget --}}
        @if($weekMaintenance->count() > 0)
            <div class="mt-8">
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-4">
                        <h3 class="text-lg font-bold flex items-center">
                            <i class='bx bx-calendar-week mr-2 text-2xl'></i>
                            Semaine Prochaine
                        </h3>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @foreach($weekMaintenance as $item)
                            <div class="p-4 hover:bg-gray-50 transition">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <p class="font-bold text-green-600">{{ $item['date']->format('d') }}</p>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-bold text-gray-800">
                                            {{ $item['plan']->equipement->nom }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            {{ $item['date']->format('l j F \à H:i') }}
                                        </p>
                                    </div>
                                    <span class="text-xs px-3 py-1 rounded-full
                                        @if($item['plan']->type === 'preventive')
                                            bg-green-100 text-green-700 font-medium
                                        @else
                                            bg-red-100 text-red-700 font-medium
                                        @endif">
                                        {{ $item['plan']->type === 'preventive' ? 'Préventive' : 'Corrective' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Footer Info --}}
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6 text-sm text-blue-800">
            <p class="font-medium flex items-center">
                <i class='bx bx-info-circle mr-2 text-lg'></i>
                Informations Importantes
            </p>
            <ul class="mt-3 space-y-2 ml-6">
                <li>✓ Les dates affichées sont vos maintenances planifiées</li>
                <li>✓ Vous êtes simplement informé des jours d'intervention</li>
                <li>✓ Consultez le calendrier complet pour plus de détails</li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentDate = new Date('{{ $now->year }}-{{ str_pad($now->month, 2, '0', STR_PAD_LEFT) }}-01');

    function previousMonth() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        updateCalendar();
    }

    function nextMonth() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        updateCalendar();
    }

    function updateCalendar() {
        const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        const monthTitle = monthNames[currentDate.getMonth()] + ' ' + currentDate.getFullYear();
        document.getElementById('monthTitle').textContent = monthTitle;

        // Recharger la page avec le mois sélectionné
        const url = new URL(window.location);
        url.searchParams.set('month', currentDate.getMonth() + 1);
        url.searchParams.set('year', currentDate.getFullYear());
        window.location.href = url.toString();
    }

    // Rafraîchir le dashboard toutes les minutes
    setInterval(() => {
        location.reload();
    }, 60000);
</script>
@endpush

@endsection
