@extends('layouts.app')

@section('title', 'Maintenance Préventive')
@section('header', 'Planning de maintenance préventive')

@section('content')
<div class="space-y-6">

    <!-- Header Actions (Matching admin.users.index style) -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex flex-1 items-center max-w-2xl">
            <form action="{{ route('technician.maintenance-plans.index') }}" method="GET" id="filterForm" class="flex flex-1 items-center gap-4">
                <!-- Search Bar -->
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class='bx bx-search text-xl'></i>
                    </span>
                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}" 
                        placeholder="Rechercher par équipement..." 
                        class="w-full pl-10 pr-4 py-2 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        autocomplete="off">
                </div>

                <!-- Type Filter -->
                <div class="flex items-center space-x-2 text-gray-500 min-w-[200px]">
                    <i class='bx bx-filter'></i>
                    <select name="type" id="typeFilter" onchange="this.form.submit()"
                        class="w-full text-sm border-none bg-gray-50 rounded-lg focus:ring-0 cursor-pointer hover:bg-gray-100 transition">
                        <option value="">Tous les types</option>
                        <option value="preventive" {{ request('type') == 'preventive' ? 'selected' : '' }}>Préventive</option>
                        <option value="corrective" {{ request('type') == 'corrective' ? 'selected' : '' }}>Corrective</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Maintenance Plans Table (Matching admin.users.index structure) -->
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-left">
                        <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Équipement</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Localisation</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Type</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Prochaine Échéance</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($plans as $plan)
                        @php
                            $prochaine = \Carbon\Carbon::parse($plan->prochaine_date);
                            $isLate = $prochaine->isPast();
                            $isSoon = !$isLate && $prochaine->diffInDays(now()) <= 7;
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition group">
                            <td class="px-8 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm border-2 border-white shadow-sm group-hover:scale-110 transition-transform">
                                            <i class='bx bx-wrench'></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $plan->equipement->nom }}</div>
                                        <div class="text-xs text-gray-500">{{ $plan->equipement->code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center text-sm text-gray-600 font-bold italic">
                                    <i class='bx bx-map-pin mr-2 text-gray-400'></i>
                                    {{ $plan->equipement->localisationRelation->name ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($plan->type === 'preventive')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Préventive
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                        Corrective
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <div class="flex items-center text-sm font-bold {{ $isLate ? 'text-red-600' : ($isSoon ? 'text-amber-600' : 'text-gray-900') }}">
                                        <i class='bx bx-calendar mr-2 text-gray-400'></i>
                                        {{ $prochaine->format('d/m/Y') }}
                                 
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end">
                                    @if($plan->activeWorkOrder)
                                        @php
                                            $canFinish = true;
                                            $deadline = $plan->prochaine_date;
                                            if ($deadline && $deadline->isFuture()) {
                                                $canFinish = false;
                                            }
                                        @endphp
                                        <form action="{{ route('technician.workorders.complete', $plan->activeWorkOrder) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                @if(!$canFinish) disabled @endif
                                                class="px-4 py-2 {{ $canFinish ? 'bg-green-600 hover:bg-green-700 shadow-green-100' : 'bg-gray-400 cursor-not-allowed' }} text-white rounded-xl text-xs font-bold transition shadow-lg flex items-center gap-2">
                                                <i class='bx {{ $canFinish ? 'bx-check-circle' : 'bx-lock-alt' }}'></i>
                                                Terminer
                                                @if(!$canFinish)
                                                    <span class="text-[9px] opacity-75">({{ $deadline->format('d/m') }})</span>
                                                @endif
                                            </button>
                                        </form>
                                    @else
                                        @php
                                            $canStart = true;
                                            $startDate = $plan->prochaine_date;
                                            // Can only start if date is today or past
                                            if ($startDate && $startDate->isFuture() && !$startDate->isToday()) {
                                                $canStart = false;
                                            }
                                        @endphp
                                        <form action="{{ route('technician.maintenance-plans.start', $plan) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                @if(!$canStart) disabled @endif
                                                class="px-4 py-2 {{ $canStart ? 'bg-blue-600 hover:bg-blue-700 shadow-blue-100' : 'bg-gray-400 cursor-not-allowed opacity-50' }} text-white rounded-xl text-xs font-bold transition shadow-lg flex items-center gap-2">
                                                <i class='bx {{ $canStart ? 'bx-play-circle' : 'bx-time-five' }}'></i>
                                                {{ $canStart ? 'Démarrer' : 'En attente' }}
                                                @if(!$canStart)
                                                    <span class="text-[9px] opacity-75">({{ $startDate->format('d/m') }})</span>
                                                @endif
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <i class='bx bx-calendar-x text-6xl mb-4 opacity-20'></i>
                                    <p class="font-semibold">Aucun planning trouvé</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($plans->hasPages())
        <div class="px-8 py-4 border-t border-gray-100 bg-gray-50">
            {{ $plans->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
