@extends('layouts.app')

@section('title', 'Maintenance Préventive')
@section('header', 'Planning de maintenance préventive')

@section('content')
<div class="space-y-6">
    <!-- Calendar Header (Month Navigation for legacy compatibility if needed) -->
    <!-- (Optional: could keep the FullCalendar native header) -->

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Calendar -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        <!-- Sidebar: Upcoming Maintenance List (RESTORED) -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 sticky top-6">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-3">
                    <h4 class="font-bold text-sm flex items-center">
                        <i class='bx bx-list-ul mr-2'></i>
                        Prochaines Étapes
                    </h4>
                </div>
                <div class="divide-y divide-gray-100 max-h-[700px] overflow-y-auto">
                    @forelse($plans as $plan)
                        @php
                            $planModel = is_array($plan) ? $plan['plan'] : $plan;
                            $planDate = is_array($plan) ? $plan['date'] : $planModel->prochaine_date;
                            $planType = is_array($plan) ? $plan['type'] : $planModel->type;
                            $planWorkOrder = is_array($plan) ? ($plan['activeWorkOrder'] ?? null) : $planModel->activeWorkOrder;
                            $prochaine = \Carbon\Carbon::parse($planDate);
                            $isLate = $prochaine->isPast();
                            $isSoon = !$isLate && $prochaine->diffInDays(now()) <= 7;
                        @endphp
                        <div class="p-3 hover:bg-gray-50 transition border-l-4 {{ $planType === 'preventive' ? 'border-blue-500' : 'border-orange-500' }}">
                            <div class="flex items-start gap-2">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-gray-900 truncate">{{ $planModel->equipement->nom }}</p>
                                    <p class="text-[10px] text-gray-500">{{ $planModel->equipement->code }}</p>
                                    <p class="text-[10px] mt-1 font-medium {{ $isLate ? 'text-red-600' : ($isSoon ? 'text-amber-600' : 'text-gray-600') }}">
                                        <i class='bx bx-calendar'></i>
                                        {{ $prochaine->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-2">
                            @php 
                                $isFuture = $prochaine->isFuture() && !$prochaine->isToday();
                            @endphp

                            @if($planWorkOrder && !$isFuture)
                                <form action="{{ route('technician.workorders.complete', $planWorkOrder) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded-lg text-[10px] font-bold transition flex items-center justify-center gap-1">
                                        <i class='bx bx-check-circle'></i> Terminer
                                    </button>
                                </form>
                            @elseif(!$isFuture)
                                <form action="{{ route('technician.maintenance-plans.start', $planModel) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="date" value="{{ $prochaine->format('Y-m-d') }}">
                                    <button type="submit" class="w-full px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-bold transition flex items-center justify-center gap-1">
                                        <i class='bx bx-play-circle'></i> Démarrer
                                    </button>
                                </form>
                            @else
                                <button type="button" disabled
                                    class="w-full px-2 py-1 bg-gray-400 cursor-not-allowed text-white rounded-lg text-[10px] font-bold transition flex items-center justify-center gap-1">
                                    <i class='bx bx-time'></i> En attente
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <i class='bx bx-calendar-x text-3xl text-gray-300 mb-2'></i>
                        <p class="text-[10px] text-gray-500">Aucune maintenance</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet" />
<style>
    :root {
        --fc-border-color: #f1f5f9;
        --fc-daygrid-event-dot-width: 6px;
        --fc-today-bg-color: #eff6ff;
    }
    .fc {
        font-family: inherit;
        background: white;
        border: none;
    }
    .fc .fc-toolbar-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
    }
    .fc .fc-toolbar {
        margin-bottom: 1.5rem !important;
        padding: 0.5rem;
    }
    .fc .fc-button {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #64748b;
        font-weight: 600;
        border-radius: 8px;
        padding: 0.4rem 0.8rem;
        font-size: 0.8rem;
        text-transform: capitalize;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    }
    .fc .fc-button-active {
        background: #4f46e5 !important;
        border-color: #4f46e5 !important;
        color: white !important;
    }
    .fc-theme-standard td, .fc-theme-standard th {
        border-color: #f1f5f9;
    }
    .fc .fc-col-header-cell {
        padding: 10px 0;
        background: #f8fafc;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.65rem;
        color: #94a3b8;
    }
    .fc .fc-daygrid-day-number {
        font-weight: 700;
        color: #475569;
        padding: 8px;
        font-size: 0.8rem;
    }
    .fc .fc-day-today .fc-daygrid-day-number {
        color: #2563eb;
        background: #dbeafe;
        border-radius: 9999px;
        width: 24px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    /* Simple Event Labels: just text, no pointer cursor */
    .fc-event {
        cursor: default;
        padding: 2px 6px;
        border-radius: 4px;
        border: none !important;
        font-weight: 600;
        font-size: 0.7rem;
        margin: 1px 2px !important;
        box-shadow: none !important;
    }
    .event-preventive {
        background: #eff6ff !important;
        color: #2563eb !important;
        border-right: 2px solid #3b82f6 !important;
    }
    .event-corrective {
        background: #fff7ed !important;
        color: #ea580c !important;
        border-right: 2px solid #f97316 !important;
    }
    .fc-event-title {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            firstDay: 1,
            height: 'auto',
            headerToolbar: {
                left: 'today prev,next',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            buttonText: {
                today: 'Auj',
                month: 'Mois',
                week: 'Sem',
                list: 'Liste'
            },
            events: @json($calendarEvents),
            eventDisplay: 'block' // Ensures titles are shown as labels
        });
        calendar.render();
    });
</script>
@endpush
@endsection
