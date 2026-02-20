@extends('layouts.app')

@section('title', 'Calendrier de Maintenance')

@section('content')
<div class="max-w-7xl mx-auto py-8 space-y-8">
    
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-gray-800">📅 Calendrier de Maintenance</h1>
            <p class="text-gray-600 mt-2">Visualisez vos interventions de maintenance planifiées</p>
        </div>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('maintenance-plans.index') }}" 
               class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                <i class='bx bx-plus mr-2'></i> Créer un plan
            </a>
        @endif
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Équipement</label>
                <select id="filter-equipement" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:ring-indigo-500">
                    <option value="">Tous les équipements</option>
                    @foreach($equipements as $eq)
                        <option value="{{ $eq->id }}">{{ $eq->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Technicien</label>
                <select id="filter-technicien" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:ring-indigo-500">
                    <option value="">Tous les techniciens</option>
                    @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Type</label>
                <select id="filter-type" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:ring-indigo-500">
                    <option value="">Tous les types</option>
                    <option value="preventive">Préventive</option>
                    <option value="corrective">Corrective</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Vue</label>
                <select id="calendar-view" class="w-full rounded-lg border-gray-300 bg-gray-50 focus:ring-indigo-500">
                    <option value="month">Mois</option>
                    <option value="quarter">Trimestre (3 mois)</option>
                    <option value="year">Année</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Calendars Container (FullCalendar) -->
    <div id="calendars-container" class="space-y-6">
        <div class="bg-white rounded-xl shadow border border-gray-200 p-4">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold">Vue calendrier</h2>
                <div class="flex items-center gap-2">
                    <select id="fc-view-select" class="rounded-lg border-gray-300 bg-gray-50 px-2 py-1">
                        <option value="dayGridMonth">Mois</option>
                        <option value="timeGridWeek">Semaine</option>
                        <option value="timeGridDay">Jour</option>
                        <option value="listWeek">Liste</option>
                    </select>
                </div>
            </div>

            <div id="maintenance-calendar"></div>
        </div>
    </div>

    <!-- List View -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Upcoming Maintenance (2 columns) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class='bx bx-calendar-check mr-2 text-indigo-600'></i>
                    Prochaines maintenances (30 jours)
                </h3>

                <div class="space-y-3">
                    @forelse($upcomingMaintenance as $maintenance)
                        <div class="flex items-start gap-4 p-4 border border-gray-200 rounded-lg hover:bg-indigo-50 transition">
                            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class='bx bx-wrench text-indigo-600 text-xl'></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-gray-800">{{ $maintenance['equipement']->nom }}</p>
                                <p class="text-sm text-gray-600">{{ $maintenance['plan']->description ?? 'Plan de maintenance' }}</p>
                                <div class="flex gap-4 mt-2">
                                    <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded font-medium">
                                        {{ format_date($maintenance['date']) }}
                                    </span>
                                    @if($maintenance['plan']->technicien)
                                        <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                            👤 {{ $maintenance['plan']->technicien->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class='bx bx-calendar-x text-4xl text-gray-300 mb-2'></i>
                            <p>Aucune maintenance prévue pour les 30 prochains jours</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Stats (1 column) -->
        <div class="space-y-4">
            <!-- Total Plans -->
            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 border border-indigo-200 rounded-xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-bold text-indigo-700 uppercase tracking-wider">Plans Actifs</p>
                    <i class='bx bx-calendar-plus text-indigo-600 text-2xl'></i>
                </div>
                <p class="text-3xl font-bold text-indigo-900">{{ $stats['total_plans'] ?? 0 }}</p>
                <p class="text-xs text-indigo-600 mt-2">{{ $stats['active_plans'] ?? 0 }} actifs</p>
            </div>

            <!-- This Month -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-bold text-green-700 uppercase tracking-wider">Ce Mois</p>
                    <i class='bx bx-calendar-check text-green-600 text-2xl'></i>
                </div>
                <p class="text-3xl font-bold text-green-900">{{ $stats['this_month_count'] ?? 0 }}</p>
                <p class="text-xs text-green-600 mt-2">interventions prévues</p>
            </div>

            <!-- Next 30 Days -->
            <div class="bg-gradient-to-br from-orange-50 to-amber-50 border border-orange-200 rounded-xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-bold text-orange-700 uppercase tracking-wider">30 Jours</p>
                    <i class='bx bx-timer text-orange-600 text-2xl'></i>
                </div>
                <p class="text-3xl font-bold text-orange-900">{{ $stats['next_30_days_count'] ?? 0 }}</p>
                <p class="text-xs text-orange-600 mt-2">interventions</p>
            </div>

            <!-- By Technician -->
            <div class="bg-white rounded-xl shadow border border-gray-200 p-6">
                <h4 class="font-bold text-gray-800 mb-3">Par Technicien</h4>
                <div class="space-y-2">
                    @foreach($stats['by_technician'] ?? [] as $tech => $count)
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-700">{{ $tech }}</p>
                            <span class="text-xs font-bold bg-indigo-100 text-indigo-700 px-2 py-1 rounded">
                                {{ $count }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500">Aucune donnée</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>

@push('styles')
<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet" />
<style>
    .fc {
        font-family: inherit;
    }

    .fc .fc-toolbar {
        margin-bottom: 0.75rem;
    }

    .fc .fc-toolbar-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: rgb(31 41 55);
    }

    .fc .fc-button {
        background: white;
        border: 1px solid rgb(209 213 219);
        color: rgb(55 65 81);
        text-transform: none;
        font-weight: 500;
        box-shadow: none;
    }

    .fc .fc-button:hover,
    .fc .fc-button:focus,
    .fc .fc-button.fc-button-active {
        background: rgb(243 244 246);
        border-color: rgb(156 163 175);
        color: rgb(17 24 39);
        box-shadow: none;
    }

    .fc-theme-standard .fc-scrollgrid,
    .fc-theme-standard td,
    .fc-theme-standard th {
        border-color: rgb(229 231 235);
    }

    .fc .fc-col-header-cell {
        background: rgb(249 250 251);
        color: rgb(107 114 128);
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 600;
    }

    .fc .fc-daygrid-day-number {
        color: rgb(55 65 81);
        font-size: 0.75rem;
        font-weight: 500;
        padding: 0.35rem;
    }

    .fc .fc-day-today {
        background: rgb(239 246 255) !important;
    }

    .fc .fc-event {
        border: none;
        border-radius: 4px;
        padding: 0 4px;
        font-size: 0.72rem;
        font-weight: 600;
        background: rgb(22 163 74);
        color: white;
    }

    .fc .fc-list-event:hover td {
        background: rgb(243 244 246);
    }
</style>
@endpush

@push('scripts')
<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filters behavior (kept)
    const filterEquipement = document.getElementById('filter-equipement');
    const filterTechnicien = document.getElementById('filter-technicien');
    const filterType = document.getElementById('filter-type');
    const calendarView = document.getElementById('calendar-view');

    function updateFilters() {
        const params = new URLSearchParams({
            equipement: filterEquipement.value || '',
            technicien: filterTechnicien.value || '',
            type: filterType.value || '',
            view: calendarView.value || 'month',
        });

        window.location.href = `{{ route('maintenance-calendar') }}?${params}`;
    }

    filterEquipement.addEventListener('change', updateFilters);
    filterTechnicien.addEventListener('change', updateFilters);
    filterType.addEventListener('change', updateFilters);
    calendarView.addEventListener('change', updateFilters);

    // Build events from server-provided upcomingMaintenance
    @php
        $fcEvents = collect($upcomingMaintenance ?? [])->map(function($m){
            $start = \Carbon\Carbon::parse($m['date'])->toIso8601String();
            $title = isset($m['equipement']) ? ($m['equipement']->nom ?? 'Maintenance') : ($m['plan']->description ?? 'Maintenance');
            return [
                'title' => $title,
                'start' => $start,
                'allDay' => false,
                'extendedProps' => [
                    'plan' => isset($m['plan']) ? ['id' => ($m['plan']->id ?? null), 'description' => ($m['plan']->description ?? null)] : null,
                    'equipement' => isset($m['equipement']) ? ['id' => ($m['equipement']->id ?? null), 'nom' => ($m['equipement']->nom ?? null)] : null,
                ],
            ];
        })->values()->all();
    @endphp

    const events = @json($fcEvents);

    // Initialize FullCalendar
    const calendarEl = document.getElementById('maintenance-calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'fr',
        firstDay: 1,
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'today prev,next',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
            today: "Aujourd'hui",
            month: 'Mois',
            week: 'Semaine',
            day: 'Jour',
            list: 'Liste'
        },
        dayMaxEventRows: 3,
        fixedWeekCount: false,
        navLinks: true,
        nowIndicator: true,
        height: 650,
        events: events,
        eventClick: function(info) {
            const ev = info.event;
            const props = ev.extendedProps || {};
            const plan = props.plan || {};
            const equip = props.equipement || {};

            // Fill modal
            document.getElementById('event-modal-title').textContent = ev.title;
            document.getElementById('event-modal-date').textContent = new Date(ev.start).toLocaleString();
            document.getElementById('event-modal-desc').textContent = plan.description || '';
            document.getElementById('event-modal-equip').textContent = equip.nom || '';

            // Show modal
            document.getElementById('event-modal').classList.remove('hidden');
        }
    });

    calendar.render();

    // View select control
    const viewSelect = document.getElementById('fc-view-select');
    viewSelect.addEventListener('change', function() {
        calendar.changeView(this.value);
    });

    viewSelect.value = 'dayGridMonth';

    // Modal close
    document.getElementById('event-modal-close').addEventListener('click', function() {
        document.getElementById('event-modal').classList.add('hidden');
    });
});
</script>
@endpush

<!-- Event modal -->
<div id="event-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="bg-white rounded-lg shadow-lg p-6 z-10 w-full max-w-md">
        <div class="flex items-start justify-between">
            <div>
                <h3 id="event-modal-title" class="text-lg font-bold"></h3>
                <p id="event-modal-date" class="text-sm text-gray-600 mt-1"></p>
            </div>
            <button id="event-modal-close" class="text-gray-500 hover:text-gray-700">✕</button>
        </div>
        <div class="mt-4">
            <p id="event-modal-desc" class="text-gray-700"></p>
            <p class="text-sm text-gray-500 mt-2">Équipement: <span id="event-modal-equip" class="font-medium"></span></p>
        </div>
    </div>
</div>

@endsection
