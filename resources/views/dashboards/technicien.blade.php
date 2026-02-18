@extends('layouts.app')

@section('title', 'Tableau de bord Technicien')

@section('content')
<div class="space-y-8">
    <!-- 1. Header Section (Date on Right) -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Bonjour, {{ Auth::user()->name }} 👋</h1>
            <p class="text-gray-500 font-medium">Récapitulatif de vos activités et performances terrain.</p>
        </div>
        
        <div class="bg-white px-5 py-2.5 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-3 text-gray-700 w-fit shrink-0">
            <i class='bx bx-calendar text-blue-600 text-xl'></i>
            <span class="text-sm font-bold uppercase tracking-tight">{{ now()->translatedFormat('d F Y') }}</span>
        </div>
    </div>

    {{-- ===== DEADLINE ALERTS CARD ===== --}}
    @php
        $alertPlans = \App\Models\MaintenancePlan::with('equipement')
            ->where('technicien_id', auth()->id())
            ->where('statut', 'actif')
            ->whereNotNull('prochaine_date')
            ->whereDate('prochaine_date', '<=', now()->addDays(7))
            ->orderBy('prochaine_date')
            ->get();
    @endphp

    @if($alertPlans->isNotEmpty())
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden border-l-4 border-l-red-500">
        {{-- Card Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50 bg-gray-50/30">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 bg-red-100 rounded-xl flex items-center justify-center text-red-600">
                    <i class='bx bx-bell bx-tada text-xl'></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Rappel</h3>
                    <p class="text-[10px] text-gray-400 font-medium">{{ $alertPlans->count() }} maintenance{{ $alertPlans->count() > 1 ? 's' : '' }} à surveiller</p>
                </div>
            </div>
            <a href="{{ route('technician.maintenance-plans.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 transition">
                <i class='bx bx-right-arrow-alt'></i> Voir tout
            </a>
        </div>

        {{-- Alert Items --}}
        <div class="divide-y divide-gray-50">
            @foreach($alertPlans as $alertPlan)
            @php
                $dueDate   = \Carbon\Carbon::parse($alertPlan->prochaine_date)->startOfDay();
                $today     = now()->startOfDay();
                $daysLeft  = $today->diffInDays($dueDate, false);

                if ($daysLeft < 0) {
                    $dotColor   = 'bg-red-500';
                    $badgeBg    = 'bg-red-100 text-red-700';
                    $message    = 'En retard de ' . abs($daysLeft) . ' jour' . (abs($daysLeft) > 1 ? 's' : '');
                    $emoji      = '⚠️';
                } elseif ($daysLeft === 0) {
                    $dotColor   = 'bg-red-500 animate-pulse';
                    $badgeBg    = 'bg-red-100 text-red-700';
                    $message    = "Prévu aujourd'hui";
                    $emoji      = '🔴';
                } elseif ($daysLeft <= 3) {
                    $dotColor   = 'bg-orange-500';
                    $badgeBg    = 'bg-orange-100 text-orange-700';
                    $message    = 'Dans ' . $daysLeft . ' jour' . ($daysLeft > 1 ? 's' : '');
                    $emoji      = '🟠';
                } else {
                    $dotColor   = 'bg-yellow-500';
                    $badgeBg    = 'bg-yellow-100 text-yellow-700';
                    $message    = 'Dans ' . $daysLeft . ' jours';
                    $emoji      = '🟡';
                }
            @endphp
            <div class="flex items-center gap-4 px-6 py-3.5 hover:bg-gray-50/50 transition-colors">
                {{-- Status dot --}}
                <div class="shrink-0 h-3 w-3 rounded-full {{ $dotColor }}"></div>

                {{-- Equipment info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900 truncate">
                        {{ $emoji }} {{ $alertPlan->equipement->nom ?? 'Équipement inconnu' }}
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Prévu le {{ $dueDate->translatedFormat('d F Y') }}
                    </p>
                </div>

                {{-- Countdown badge --}}
                <span class="shrink-0 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ $badgeBg }}">
                    {{ $message }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
     <!-- 2. Graphe Central (Pleine Largeur) -->
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition duration-300">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h4 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <i class='bx bx-bar-chart-alt-2 text-indigo-600'></i>
                    Mes Interventions Mensuelles
                </h4>
                <p class="text-xs text-gray-500 font-medium mt-1">Évolution de votre productivité en {{ date('Y') }}</p>
            </div>
            <div class="hidden md:flex items-center gap-2">
                <span class="h-3 w-3 bg-indigo-600 rounded-full"></span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Terminées</span>
            </div>
        </div>
        <div class="h-64 md:h-80">
            <canvas id="technicianMonthlyChart"></canvas>
        </div>
    </div>


     <!-- 3. Stats Grid (Pleine Largeur) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
        <!-- À faire -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition group text-center md:text-left">
            <div class="flex items-center justify-between mb-4">
                <div class="h-11 w-11 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors mx-auto md:mx-0">
                    <i class='bx bx-list-ul text-2xl'></i>
                </div>
                <span class="hidden md:block text-[10px] font-bold text-gray-400 uppercase tracking-widest uppercase tracking-widest">À faire</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 leading-none"><span data-target="{{ $stats['todo_count'] }}">0</span></div>
             <p class="md:hidden text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">À faire</p>
        </div>

        <!-- Urgentes -->
        <div class="bg-white p-6 rounded-3xl border border-red-50 shadow-sm hover:shadow-md transition group border-l-4 border-l-red-500 text-center md:text-left">
            <div class="flex items-center justify-between mb-4">
                <div class="h-11 w-11 bg-red-50 rounded-2xl flex items-center justify-center group-hover:bg-red-600 group-hover:text-white transition-colors mx-auto md:mx-0">
                    <i class='bx bxs-alarm-exclamation text-2xl'></i>
                </div>
                <span class="hidden md:block text-[10px] font-bold text-red-400 uppercase tracking-widest uppercase tracking-widest">Urgent</span>
            </div>
            <div class="text-3xl font-bold text-red-600 leading-none"><span data-target="{{ $stats['urgent_count'] }}">0</span></div>
             <p class="md:hidden text-[10px] font-bold text-red-400 uppercase tracking-widest mt-1">Urgents</p>
        </div>

        <!-- Terminées -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition group text-center md:text-left">
            <div class="flex items-center justify-between mb-4">
                <div class="h-11 w-11 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors mx-auto md:mx-0">
                    <i class='bx bx-check-double text-2xl'></i>
                </div>
                <span class="hidden md:block text-[10px] font-bold text-emerald-400 uppercase tracking-widest uppercase tracking-widest">Succès</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 leading-none"><span data-target="{{ $stats['completed_count'] }}">0</span></div>
            <p class="md:hidden text-[10px] font-bold text-emerald-400 uppercase tracking-widest mt-1">Terminées</p>
        </div>

        <!-- Performance -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition group text-center md:text-left">
            <div class="flex items-center justify-between mb-4">
                <div class="h-11 w-11 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-colors mx-auto md:mx-0">
                    <i class='bx bx-timer text-2xl'></i>
                </div>
                <span class="hidden md:block text-[10px] font-bold text-amber-500 uppercase tracking-widest">Moyenne</span>
            </div>
            <div class="text-2xl font-bold text-gray-900 leading-none">{{ $avgTaskDuration }}</div>
            <p class="md:hidden text-[10px] font-bold text-amber-500 uppercase tracking-widest mt-1">Temps Moyen</p>
        </div>

        <!-- Maintenance du Jour -->
        <div class="bg-white p-6 rounded-3xl border border-purple-100 shadow-sm hover:shadow-md transition group text-center md:text-left border-l-4 border-l-purple-500">
            <div class="flex items-center justify-between mb-4">
                <div class="h-11 w-11 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors mx-auto md:mx-0">
                    <i class='bx bx-calendar-check text-2xl'></i>
                </div>
                <span class="hidden md:block text-[10px] font-bold text-purple-500 uppercase tracking-widest">Préventif</span>
            </div>
            <div class="text-2xl font-bold text-gray-900 leading-none">
                <span data-target="{{ $stats['maintenance_due_today'] }}">0</span>
            </div>
            <p class="md:hidden text-[10px] font-bold text-purple-500 uppercase tracking-widest mt-1">À faire</p>
            @if($stats['maintenance_upcoming'] > 0)
                <p class="text-[10px] text-gray-400 mt-2 font-medium">+{{ $stats['maintenance_upcoming'] }} à venir (7j)</p>
            @endif
        </div>
    </div>

    <!-- 4. Section Basse : Tableau & Profil (Cote à Cote) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Tableau des Tâches (Large) -->
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between px-2">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <i class='bx bx-task text-blue-600'></i>
                    Mes Tâches Actuelles
                </h2>
                <div class="flex items-center gap-3">
                    <a href="{{ route('technician.workorders.index') }}" class="flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-100 rounded-xl text-xs font-bold text-blue-600 hover:bg-gray-50 hover:text-blue-700 transition shadow-sm">
                        <i class='bx bx-list-ul text-sm'></i>
                        Voir Workorders
                    </a>
                    <a href="{{ route('technician.workorders.history') }}" class="flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-100 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition shadow-sm">
                        <i class='bx bx-history text-sm'></i>
                        Voir Historique
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Équipement</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">Priorité</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($todoTasks as $task)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 group-hover:text-blue-600 transition-colors">
                                            <i class='bx bx-wrench text-xl'></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 text-sm italic group-not-italic">{{ $task->equipement->nom ?? 'Inconnu' }}</div>
                                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest uppercase tracking-widest">CODE: {{ $task->equipement->code ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $prioClasses = [
                                            'basse' => 'bg-green-100 text-green-700',
                                            'normale' => 'bg-blue-100 text-blue-700',
                                            'haute' => 'bg-orange-100 text-orange-700',
                                            'urgente' => 'bg-red-100 text-red-700',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $prioClasses[$task->priorite] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $task->priorite }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="text-sm font-bold text-gray-900 leading-tight font-bold">{{ $task->created_at->format('d M') }}</div>
                                    <div class="text-[10px] text-gray-400 font-medium font-medium">{{ $task->created_at->format('H:i') }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center opacity-40">
                                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest uppercase tracking-widest">Aucune tâche assignée</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 5. Prochaines Maintenances Préventives -->
            <div class="flex items-center justify-between px-2 pt-6">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <i class='bx bx-calendar-check text-purple-600'></i>
                    Prochaines Maintenances Préventives
                </h2>
                <a href="{{ route('technician.maintenance-plans.index') }}" class="flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-100 rounded-xl text-xs font-bold text-purple-600 hover:bg-gray-50 hover:text-purple-700 transition shadow-sm">
                    <i class='bx bx-list-ul text-sm'></i>
                    Voir Tout
                </a>
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Équipement</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">Intervalle</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Prévu le</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($upcomingMaintenance as $plan)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                                            <i class='bx bx-wrench text-xl'></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 text-sm italic group-not-italic">{{ $plan['plan']->equipement->nom ?? 'Inconnu' }}</div>
                                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">CODE: {{ $plan['plan']->equipement->code ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                                        @if($plan['plan']->rrule)
                                            RRULE
                                        @else
                                            {{ $plan['plan']->interval_jours }} jours
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @php
                                        $date = \Carbon\Carbon::parse($plan['date']);
                                        $isToday = $date->isToday();
                                        $isTomorrow = $date->isTomorrow();
                                        $canStart = ($date->isPast() || $date->isToday());
                                    @endphp
                                    <div class="flex items-center justify-end gap-4">
                                        <div class="text-right">
                                            <div class="text-sm font-bold {{ $isToday ? 'text-green-600' : ($isTomorrow ? 'text-blue-600' : 'text-gray-900') }}">
                                                {{ $isToday ? "Aujourd'hui" : ($isTomorrow ? "Demain" : $date->translatedFormat('d F Y')) }}
                                            </div>
                                            <div class="text-[10px] text-gray-400 font-medium">
                                                {{ $date->diffForHumans() }}
                                            </div>
                                        </div>
                                        
                                        <form action="{{ route('technician.maintenance-plans.start', $plan['plan']) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
                                            <button type="submit" 
                                                @if(!$canStart) disabled @endif
                                                class="px-3 py-1.5 rounded-xl text-[10px] font-bold uppercase tracking-wider transition
                                                {{ $canStart ? 'bg-purple-600 text-white hover:bg-purple-700 shadow-sm' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}">
                                                {{ $canStart ? 'Démarrer' : 'Attente' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center opacity-40">
                                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest uppercase tracking-widest">Aucune maintenance prévue</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Profil & Liens Sidebar (À côté du tableau) -->
        <div class="space-y-6 lg:mt-11">
             <!-- Profile Card -->
            <!-- Profil Overview (Version Épurée) -->
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm border-t-4 border-t-indigo-600 relative group transition-all hover:shadow-md">
                <a href="{{ route('profile.edit') }}" class="absolute top-4 right-4 h-8 w-8 bg-gray-50 text-gray-400 rounded-lg flex items-center justify-center hover:bg-indigo-50 hover:text-indigo-600 transition shadow-sm" title="Modifier le profil">
                    <i class='bx bx-edit-alt text-lg'></i>
                </a>

                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Votre Profil</h3>
                
                <div class="flex items-center gap-4 mb-6">
                    <div class="h-16 w-16 rounded-2xl overflow-hidden border-2 border-indigo-50 bg-indigo-50 flex items-center justify-center shadow-sm">
                        <img src="{{ Auth::user()->profile_photo_url }}" 
                             alt="{{ Auth::user()->name }}"
                             class="h-full w-full object-cover">
                    </div>
                    <div>
                        <div class="font-bold text-gray-900 text-lg leading-tight uppercase">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-gray-500 font-medium">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-50">
                     <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-400 font-bold uppercase tracking-widest">Rôle</span>
                        <span class="font-black text-emerald-600 uppercase tracking-widest bg-emerald-50 px-2 py-0.5 rounded-md">TECHNICIEN</span>
                     </div>
                </div>
            </div>

       
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('technicianMonthlyChart').getContext('2d');
        const monthlyData = @json($monthlyInterventions);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthlyData.map(item => item.month),
                datasets: [{
                    label: 'Interventions',
                    data: monthlyData.map(item => item.count),
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.05)',
                    borderWidth: 4,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, color: '#94a3b8', font: { weight: '600' } }, grid: { color: 'rgba(0,0,0,0.03)' } },
                    x: { ticks: { color: '#94a3b8', font: { weight: '600' } }, grid: { display: false } }
                }
            }
        });

        const counters = document.querySelectorAll('[data-target]');
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target')) || 0;
            const duration = 1000;
            const startTime = performance.now();
            function update(now) {
                const elapsed = now - startTime;
                const progress = Math.min(elapsed / duration, 1);
                counter.textContent = Math.floor(progress * target);
                if (progress < 1) requestAnimationFrame(update);
                else counter.textContent = target;
            }
            requestAnimationFrame(update);
        });
    });
</script>
@endpush
@endsection