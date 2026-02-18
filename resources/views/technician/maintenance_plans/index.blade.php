@extends('layouts.app')

@section('title', 'Maintenance Préventive')
@section('header', 'Planning de maintenance préventive')

@section('content')
<div class="space-y-6">
@php
    $now = $currentDate ?? now();
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
@endphp

    <!-- Header Actions -->
    <!-- Header Actions Removed (Search & Filter) -->
    <!-- Calendar View -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Calendar -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
                <!-- Calendar Header -->
                <div class="bg-gradient-to-r from-indigo-600 to-blue-600 text-white px-6 py-4">
                    <div class="flex items-center justify-between">
                        <a href="?month={{ $now->copy()->subMonth()->month }}&year={{ $now->copy()->subMonth()->year }}" 
                           class="px-4 py-2 rounded-lg hover:bg-white/20 transition flex items-center gap-2">
                            <i class='bx bx-chevron-left text-xl'></i>
                        </a>
                        
                        <h3 class="text-xl font-bold text-center">
                            <i class='bx bx-calendar mr-2'></i>
                            {{ $now->format('F Y') }}
                        </h3>
                        
                        <a href="?month={{ $now->copy()->addMonth()->month }}&year={{ $now->copy()->addMonth()->year }}" 
                           class="px-4 py-2 rounded-lg hover:bg-white/20 transition flex items-center gap-2">
                            <i class='bx bx-chevron-right text-xl'></i>
                        </a>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="p-4 sm:p-6">
                    <!-- Days Header -->
                    <div class="grid grid-cols-7 gap-1 mb-2">
                        @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $day)
                            <div class="p-2 text-center font-bold text-indigo-700 text-xs sm:text-sm bg-indigo-50 rounded">
                                {{ $day }}
                            </div>
                        @endforeach
                    </div>

                    <!-- Days Grid -->
                    @foreach($weeks as $week)
                        <div class="grid grid-cols-7 gap-1 mb-1">
                            @foreach($week['days'] as $day)
                                @php
                                    $dateKey = $day->format('Y-m-d');
                                    $items = $maintenanceByDate[$dateKey] ?? [];
                                    $isToday = $day->format('Y-m-d') === now()->format('Y-m-d');
                                    $isCurrentMonth = $day->month === $now->month;
                                @endphp
                                
                                <div class="aspect-square p-1 sm:p-2 rounded-lg border transition hover:shadow-lg cursor-pointer group relative min-h-[60px]
                                    @if($isToday)
                                        bg-green-50 border-2 border-green-500 ring-2 ring-green-200
                                    @elseif(!$isCurrentMonth)
                                        bg-gray-50 border-gray-200
                                    @else
                                        bg-white border-gray-200
                                    @endif
                                    @if(count($items) > 0)
                                        hover:bg-indigo-50
                                    @endif">
                                    
                                    <!-- Day Number -->
                                    <p class="text-xs sm:text-sm font-bold mb-1
                                        @if($isToday)
                                            text-green-700
                                        @elseif(!$isCurrentMonth)
                                            text-gray-400
                                        @else
                                            text-gray-700
                                        @endif">
                                        {{ $day->format('d') }}
                                    </p>

                                    <!-- Maintenance Indicators -->
                                    @if(count($items) > 0)
                                        <div class="flex flex-wrap gap-0.5">
                                            @foreach(array_slice($items, 0, 3) as $item)
                                                <div class="w-2 h-2 rounded-full
                                                    @if($item['plan']->type === 'preventive')
                                                        bg-blue-500
                                                    @else
                                                        bg-orange-500
                                                    @endif"></div>
                                            @endforeach
                                            @if(count($items) > 3)
                                                <span class="text-[8px] text-gray-600 font-bold">+{{ count($items) - 3 }}</span>
                                            @endif
                                        </div>

                                        <!-- Tooltip on Hover -->
                                        <div class="hidden group-hover:block absolute left-0 top-full mt-1 z-50 bg-gray-900 text-white text-xs rounded-lg p-3 whitespace-nowrap shadow-xl min-w-max">
                                            <div class="font-bold mb-2">{{ count($items) }} maintenance(s)</div>
                                            @foreach(array_slice($items, 0, 5) as $item)
                                                <div class="text-[10px] mb-1 flex items-center gap-1">
                                                    <div class="w-1.5 h-1.5 rounded-full
                                                        @if($item['plan']->type === 'preventive')
                                                            bg-blue-400
                                                        @else
                                                            bg-orange-400
                                                        @endif"></div>
                                                    {{ $item['plan']->equipement->nom }}
                                                </div>
                                            @endforeach
                                            @if(count($items) > 5)
                                                <div class="text-[10px] text-gray-400 mt-1">et {{ count($items) - 5 }} de plus...</div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                    <!-- Legend -->
                    <div class="flex items-center justify-center gap-4 sm:gap-6 mt-6 pt-4 border-t border-gray-200 text-xs sm:text-sm flex-wrap">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <span class="text-gray-700">Préventive</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full border-2 border-green-700"></div>
                            <span class="text-gray-700">Aujourd'hui</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar: Upcoming Maintenance List -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 sticky top-6">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-3">
                    <h4 class="font-bold text-sm flex items-center">
                        <i class='bx bx-list-ul mr-2'></i>
                        Prochaines Maintenances
                    </h4>
                </div>
                <div class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto">
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
                        <div class="p-3 hover:bg-gray-50 transition">
                            <div class="flex items-start gap-2">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-lg
                                    @if($planType === 'preventive')
                                        bg-blue-100 text-blue-600
                                    @else
                                        bg-orange-100 text-orange-600
                                    @endif">
                                    <i class='bx bx-wrench'></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-900 truncate">{{ $planModel->equipement->nom }}</p>
                                    <p class="text-xs text-gray-500">{{ $planModel->equipement->code }}</p>
                                    <p class="text-xs mt-1 font-medium {{ $isLate ? 'text-red-600' : ($isSoon ? 'text-amber-600' : 'text-gray-600') }}">
                                        <i class='bx bx-calendar'></i>
                                        {{ $prochaine->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-2 text-xs font-mono text-blue-600 bg-blue-50 px-2 py-0.5 rounded flex items-center gap-1 w-fit mb-2">
                                <i class='bx bx-repeat'></i>
                                Occurrence
                            </div>
                            <div class="mt-2">
                                @if($planWorkOrder)
                                    @php
                                        $canFinish = !$prochaine->isFuture();
                                    @endphp
                                    <form action="{{ route('technician.workorders.complete', $planWorkOrder) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                            @if(!$canFinish) disabled @endif
                                            class="w-full px-3 py-1.5 {{ $canFinish ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed' }} text-white rounded-lg text-xs font-bold transition flex items-center justify-center gap-1">
                                            <i class='bx bx-check-circle'></i>
                                            Terminer
                                        </button>
                                    </form>
                                @else
                                    @php
                                        $canStart = !($prochaine->isFuture() && !$prochaine->isToday());
                                    @endphp
                                    <form action="{{ route('technician.maintenance-plans.start', $planModel) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="date" value="{{ $prochaine->format('Y-m-d') }}">
                                        <button type="submit" 
                                            @if(!$canStart) disabled @endif
                                            class="w-full px-3 py-1.5 {{ $canStart ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-400 cursor-not-allowed' }} text-white rounded-lg text-xs font-bold transition flex items-center justify-center gap-1">
                                            <i class='bx bx-play-circle'></i>
                                            {{ $canStart ? 'Démarrer' : 'En attente' }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <i class='bx bx-calendar-x text-4xl text-gray-300 mb-2'></i>
                            <p class="text-sm text-gray-500">Aucune maintenance</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
