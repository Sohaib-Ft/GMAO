@props([
    'data' => [],
    'previousUrl' => null,
    'nextUrl' => null,
])

<div class="w-full bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-indigo-600 to-blue-600 text-white px-6 py-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold">
                {{ $data['month_name'] ?? 'Calendrier' }} {{ $data['year'] ?? '' }}
            </h2>
            <div class="flex gap-2">
                @if($previousUrl)
                    <a href="{{ $previousUrl }}" class="p-2 bg-white/20 hover:bg-white/30 rounded-lg transition">
                        <i class='bx bx-chevron-left text-xl'></i>
                    </a>
                @endif
                <a href="#" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg transition text-sm font-medium">
                    Aujourd'hui
                </a>
                @if($nextUrl)
                    <a href="{{ $nextUrl }}" class="p-2 bg-white/20 hover:bg-white/30 rounded-lg transition">
                        <i class='bx bx-chevron-right text-xl'></i>
                    </a>
                @endif
            </div>
        </div>
        <p class="text-indigo-100 text-sm">{{ $data['summary'] ?? 'Calendrier de maintenance' }}</p>
    </div>

    {{-- Days of Week Header --}}
    <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
        @foreach(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'] as $day)
            <div class="p-4 text-center font-bold text-gray-700 text-sm">
                {{ substr($day, 0, 3) }}
            </div>
        @endforeach
    </div>

    {{-- Calendar Grid --}}
    <div class="divide-y divide-gray-200">
        @foreach($data['weeks'] ?? [] as $week)
            <div class="grid grid-cols-7">
                @foreach($week as $day)
                    <div class="aspect-square p-3 border-r border-gray-200 @if(!$loop->last) @endif
                        @if(!$day['is_current_month']) bg-gray-50 @else bg-white @endif
                        hover:bg-indigo-50 transition relative group cursor-pointer
                        {{ $day['day_of_week'] === 7 ? 'border-r-0 border-b' : '' }}">
                        
                        {{-- Date --}}
                        <div class="text-sm font-bold @if(!$day['is_current_month']) text-gray-400 @else text-gray-700 @endif">
                            {{ $day['day'] }}
                        </div>

                        {{-- Occurrence Dot --}}
                        @if($day['has_occurrence'])
                            <div class="mt-1 flex gap-0.5 flex-wrap">
                                @foreach($day['occurrences'] as $occurrence)
                                    <div class="w-2 h-2 bg-indigo-600 rounded-full"></div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Hover Tooltip --}}
                        @if($day['has_occurrence'])
                            <div class="absolute z-50 bottom-full left-1/2 transform -translate-x-1/2 mb-2 
                                bg-gray-900 text-white text-xs py-2 px-3 rounded shadow-lg 
                                opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap
                                pointer-events-none">
                                <p class="font-bold mb-1">Maintenance</p>
                                @foreach($day['occurrences'] as $occurrence)
                                    <p>{{ $occurrence->format('H:i') }}</p>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    {{-- Legend --}}
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center gap-6 text-sm">
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 bg-indigo-600 rounded-full"></div>
            <span class="text-gray-600">Date de maintenance</span>
        </div>
        @if(count($data['occurrences'] ?? []) > 0)
            <div class="text-gray-600">
                <strong>{{ count($data['occurrences']) }}</strong> 
                occurrence@if(count($data['occurrences']) > 1)s@endif ce mois
            </div>
        @endif
    </div>
</div>
