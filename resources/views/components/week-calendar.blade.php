@props([
    'date' => null,
    'maintenance' => [],
])

@php
    $date = $date ?? now();
    $startOfWeek = $date->copy()->startOfWeek();
    $endOfWeek = $date->copy()->endOfWeek();
    
    // Créer la grille hebdomadaire
    $week = [];
    $current = $startOfWeek->copy();
    while ($current <= $endOfWeek) {
        $week[] = $current->copy();
        $current->addDay();
    }
@endphp

<div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-indigo-600 to-blue-600 text-white px-6 py-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold">
                Semaine du {{ $startOfWeek->format('d M') }} au {{ $endOfWeek->format('d M Y') }}
            </h2>
            <div class="flex gap-2">
                <a href="#" class="p-2 bg-white/20 hover:bg-white/30 rounded-lg transition">
                    <i class='bx bx-chevron-left text-xl'></i>
                </a>
                <a href="#" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg transition text-sm font-medium">
                    Aujourd'hui
                </a>
                <a href="#" class="p-2 bg-white/20 hover:bg-white/30 rounded-lg transition">
                    <i class='bx bx-chevron-right text-xl'></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Weekly Grid --}}
    <div class="grid grid-cols-7 gap-px bg-gray-200 p-px">
        @foreach($week as $day)
            @php
                $dayMaintenance = array_filter($maintenance, function($m) use ($day) {
                    return $m['date']->format('Y-m-d') === $day->format('Y-m-d');
                });
                $isToday = $day->isToday();
                $isPast = $day->isPast();
            @endphp
            
            <div class="bg-white @if($isToday) border-2 border-indigo-600 @else border border-gray-200 @endif min-h-80 p-4 flex flex-col">
                
                {{-- Day Header --}}
                <div class="flex items-center justify-between mb-4 pb-3 border-b @if($isToday) border-indigo-600 @else border-gray-200 @endif">
                    <div>
                        <p class="text-sm font-bold text-gray-500 uppercase">{{ $day->format('D') }}</p>
                        <p class="text-2xl font-bold @if($isToday) text-indigo-600 @else text-gray-800 @endif">
                            {{ $day->format('d') }}
                        </p>
                        <p class="text-xs text-gray-400">{{ $day->format('M') }}</p>
                    </div>
                    @if($isToday)
                        <span class="px-2 py-1 bg-indigo-600 text-white text-xs rounded-full font-bold">
                            Aujourd'hui
                        </span>
                    @endif
                </div>

                {{-- Maintenance Items --}}
                <div class="flex-1 space-y-2 overflow-y-auto">
                    @forelse($dayMaintenance as $item)
                        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-3 text-sm hover:shadow-md transition cursor-pointer group">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="font-bold text-indigo-900">{{ $item['plan']->equipement->nom }}</p>
                                    <p class="text-xs text-indigo-600 mt-1">
                                        <i class='bx bx-time-five text-xs'></i>
                                        {{ $item['date']->format('H:i') }}
                                    </p>
                                    @if($item['plan']->technicien)
                                        <p class="text-xs text-indigo-600 mt-1">
                                            <i class='bx bx-user text-xs'></i>
                                            {{ $item['plan']->technicien->name }}
                                        </p>
                                    @endif
                                </div>
                                <i class='bx bx-chevron-right text-indigo-400 group-hover:text-indigo-600 transition'></i>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400">
                            <i class='bx bx-calendar-x text-2xl'></i>
                            <p class="text-xs mt-2">Aucune maintenance</p>
                        </div>
                    @endforelse
                </div>

                {{-- Day Footer --}}
                @if(count($dayMaintenance) > 0)
                    <div class="mt-4 pt-3 border-t border-gray-200 text-xs text-gray-600">
                        {{ count($dayMaintenance) }} intervention@if(count($dayMaintenance) > 1)s@endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
