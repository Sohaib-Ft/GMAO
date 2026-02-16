@extends('layouts.app')

@section('title', 'Admin Dashboard')


@section('content')
<div class="space-y-8">
    <!-- 1. Header Section (Date on Right) -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Bonjour, {{ Auth::user()->name }} 👋</h1>
            <p class="text-gray-500 font-medium">Vue d'ensemble de la maintenance et des performances du système.</p>
        </div>
        
        <div class="bg-white px-5 py-2.5 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-3 text-gray-700 w-fit shrink-0">
            <i class='bx bx-calendar text-blue-600 text-xl'></i>
            <span class="text-sm font-bold uppercase tracking-tight">{{ now()->translatedFormat('d F Y') }}</span>
        </div>
    </div>

    <!-- 1. Grid Statistiques (Style Employé) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Utilisateurs -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition group text-center md:text-left">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors mx-auto md:mx-0">
                    <i class='bx bx-group text-2xl'></i>
                </div>
                <span class="hidden md:block text-xs font-bold text-gray-400 uppercase tracking-widestAlpha">Utilisateurs</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 leading-none"><span id="totalUsersCounter" data-target="{{ $stats['total_users'] }}">0</span></div>
            <div class="md:hidden text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Total Utilisateurs</div>
        </div>

        <!-- Techniciens -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition group text-center md:text-left">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors mx-auto md:mx-0">
                    <i class='bx bx-wrench text-2xl'></i>
                </div>
                <span class="hidden md:block text-xs font-bold text-indigo-400 uppercase tracking-widest">Equipe</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 leading-none"><span id="techniciansCounter" data-target="{{ $stats['technicians_count'] }}">0</span></div>
             <div class="md:hidden text-[10px] font-bold text-indigo-400 uppercase tracking-widest mt-1">Techniciens</div>
        </div>

        <!-- Utilisateurs Standards -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition group text-center md:text-left">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors mx-auto md:mx-0">
                    <i class='bx bx-user text-2xl'></i>
                </div>
                <span class="hidden md:block text-xs font-bold text-emerald-400 uppercase tracking-widest">Employés</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 leading-none"><span id="standardUsersCounter" data-target="{{ $stats['standard_users_count'] }}">0</span></div>
             <div class="md:hidden text-[10px] font-bold text-emerald-400 uppercase tracking-widest mt-1">Employés</div>
        </div>

        <!-- Total Équipements -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition group text-center md:text-left">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-colors mx-auto md:mx-0">
                    <i class='bx bx-box text-2xl'></i>
                </div>
                <span class="hidden md:block text-xs font-bold text-amber-400 uppercase tracking-widest">Équipements</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 leading-none"><span id="equipmentsCounter" data-target="{{ $stats['equipments_count'] }}">0</span></div>
             <div class="md:hidden text-[10px] font-bold text-amber-400 uppercase tracking-widest mt-1">Équipements</div>
        </div>
    </div>

     <!-- 3. Graphiques de Performance (Pleine Largeur) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Interventions par Mois -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition duration-300">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class='bx bx-line-chart text-blue-600'></i>
                        Interventions Mensuelles
                    </h3>
                    <p class="text-xs text-gray-500 font-medium mt-1">Performance sur {{ date('Y') }}</p>
                </div>
            </div>
            <div class="h-64">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- Répartition des Statuts -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition duration-300">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class='bx bx-pie-chart-alt-2 text-indigo-600'></i>
                        Répartition des Statuts
                    </h3>
                    <p class="text-xs text-gray-500 font-medium mt-1">État global des demandes</p>
                </div>
            </div>
            <div class="h-64">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>


    <!-- 2. Alertes de Maintenance (Pleine Largeur) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Demandes en Attente -->
        <div class="bg-white p-6 rounded-3xl border border-orange-100 shadow-sm hover:shadow-md transition group border-l-4 border-l-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-orange-600 uppercase tracking-widest mb-1">En Attente</p>
                    <p class="text-3xl font-bold text-gray-900"><span id="pendingRequestsCounter" data-target="{{ $stats['pending_requests'] }}">0</span></p>
                    <p class="text-xs text-gray-500 mt-1 font-medium">Nouvelles tâches</p>
                </div>
                <div class="h-16 w-16 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-colors">
                    <i class='bx bx-time-five text-3xl'></i>
                </div>
            </div>
        </div>

        <!-- Tâches en Cours -->
        <div class="bg-white p-6 rounded-3xl border border-blue-100 shadow-sm hover:shadow-md transition group border-l-4 border-l-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-1">En Cours</p>
                    <p class="text-3xl font-bold text-gray-900"><span id="tasksInProgressCounter" data-target="{{ $stats['tasks_in_progress'] }}">0</span></p>
                    <p class="text-xs text-gray-500 mt-1 font-medium">Interventions actives</p>
                </div>
                <div class="h-16 w-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <i class='bx bx-loader-alt text-3xl'></i>
                </div>
            </div>
        </div>

        <!-- Pannes Critiques -->
        <div class="bg-white p-6 rounded-3xl border border-red-100 shadow-sm hover:shadow-md transition group border-l-4 border-l-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-red-600 uppercase tracking-widest mb-1">Critique</p>
                    <p class="text-3xl font-bold text-gray-900"><span id="criticalFailuresCounter" data-target="{{ $stats['critical_failures'] }}">0</span></p>
                    <p class="text-xs text-gray-500 mt-1 font-medium">Priorité urgente</p>
                </div>
                <div class="h-16 w-16 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center group-hover:bg-red-600 group-hover:text-white transition-colors">
                    <i class='bx bx-error text-3xl'></i>
                </div>
            </div>
        </div>
    </div>

     <!-- 4. Leaders Grid (Pleine Largeur) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Top Technicien -->
        <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm group hover:translate-y-[-2px] transition-transform duration-300">
            <div class="flex items-center gap-4">
                <div class="h-11 w-11 bg-sky-50 text-sky-600 rounded-2xl flex items-center justify-center text-xl group-hover:bg-sky-600 group-hover:text-white transition-colors">
                    <i class='bx bx-user-check'></i>
                </div>
                <div class="flex-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Technicien Top</p>
                    <p class="font-bold text-gray-900 leading-tight leading-tight">{{ $stats['top_technician']->name ?? 'N/A' }}</p>
                </div>
                <div class="text-right">
                     <span class="text-sm font-bold text-sky-600" id="topTechnicianCounter" data-target="{{ $stats['top_technician']->technicien_work_orders_count ?? 0 }}">0</span>
                </div>
            </div>
        </div>

        <!-- Top Équipement -->
        <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm group hover:translate-y-[-2px] transition-transform duration-300">
            <div class="flex items-center gap-4">
                <div class="h-11 w-11 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center text-xl group-hover:bg-rose-600 group-hover:text-white transition-colors">
                    <i class='bx bxs-hot'></i>
                </div>
                <div class="flex-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Équipement Critique</p>
                    <p class="font-bold text-gray-900 leading-tight leading-tight">{{ $stats['most_claimed_equipment']->nom ?? 'N/A' }}</p>
                </div>
                <div class="text-right">
                     <span class="text-sm font-bold text-rose-600" id="topEquipmentCounter" data-target="{{ $stats['most_claimed_equipment']->work_orders_count ?? 0 }}">0</span>
                </div>
            </div>
        </div>

        <!-- Top Demandeur -->
        <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm group hover:translate-y-[-2px] transition-transform duration-300">
            <div class="flex items-center gap-4">
                <div class="h-11 w-11 bg-fuchsia-50 text-fuchsia-600 rounded-2xl flex items-center justify-center text-xl group-hover:bg-fuchsia-600 group-hover:text-white transition-colors">
                    <i class='bx bxs-megaphone'></i>
                </div>
                <div class="flex-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Top Demandeur</p>
                    <p class="font-bold text-gray-900 leading-tight leading-tight">{{ $stats['top_claimant']->name ?? 'N/A' }}</p>
                </div>
                <div class="text-right">
                     <span class="text-sm font-bold text-fuchsia-600" id="topClaimantCounter" data-target="{{ $stats['top_claimant']->employe_work_orders_count ?? 0 }}">0</span>
                </div>
            </div>
        </div>
    </div>

   
   

    <!-- 5. Section Basse : Tableau & Profil (Cote à Cote) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Tableau des Activités (Large) -->
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between px-2">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <i class='bx bx-history text-blue-600'></i>
                    Dernières Activités
                </h2>
                <a href="{{ route('admin.workorders.index') }}" class="flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-100 rounded-xl text-xs font-bold text-blue-600 hover:bg-gray-50 hover:text-blue-700 transition shadow-sm">
                    <i class='bx bx-list-ul text-sm'></i>
                    Voir tout
                </a>
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest uppercase tracking-widest">Employé</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest uppercase tracking-widest text-center">Statut</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($recentRequests as $request)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold">
                                            {{ substr(strtoupper($request->employe->name ?? 'N/A'), 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $request->employe->name ?? 'N/A' }}</p>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest uppercase tracking-widest">{{ $request->equipement->nom ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusClasses = [
                                            'nouvelle' => 'bg-blue-100 text-blue-800',
                                            'affectee' => 'bg-purple-100 text-purple-800',
                                            'en_cours' => 'bg-indigo-100 text-indigo-800',
                                            'terminee' => 'bg-green-100 text-green-800',
                                            'annulee' => 'bg-gray-100 text-gray-800',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusClasses[$request->statut] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ str_replace('_', ' ', $request->statut) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="text-sm font-bold text-gray-900 leading-tight font-bold">{{ $request->created_at->format('d M') }}</div>
                                    <div class="text-[10px] text-gray-400 font-medium font-medium">{{ $request->created_at->format('H:i') }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center opacity-40">
                                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest uppercase tracking-widest">Aucune donnée récente</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Profil Overview (À côté du tableau) -->
        <div class="space-y-6 lg:mt-11">
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm border-t-4 border-t-blue-600">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Administrateur</h3>
                <div class="flex items-center gap-4 mb-6">
                    <div class="h-14 w-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                        <i class='bx bxs-user-circle text-4xl'></i>
                    </div>
                    <div>
                        <div class="font-bold text-gray-900 text-lg leading-tight uppercase">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-gray-500 font-medium">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-50 space-y-3">
                     <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-400 font-bold uppercase uppercase tracking-widest"></span>
                        <span class="font-black text-blue-600 uppercase tracking-widest bg-blue-50 px-2 py-0.5 rounded-md"> AdminISTRATEUR</span>
                     </div>
                </div>
            </div>
            
            <!-- Quick Link / Shortcut -->

        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Graphique Mensuel
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyData = @json($monthlyInterventions);
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: monthlyData.map(item => item.month),
                datasets: [{
                    label: 'Interventions',
                    data: monthlyData.map(item => item.count),
                    backgroundColor: '#3b82f6',
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { weight: '600' }, color: '#9ca3af' } },
                    x: { grid: { display: false }, ticks: { font: { weight: '600' }, color: '#9ca3af' } }
                }
            }
        });

        // Graphique Statuts
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusData = @json($statusDistribution);
        const statusColors = {
            'nouvelle': '#3b82f6',
            'affectee': '#a855f7',
            'en_cours': '#6366f1',
            'terminee': '#10b981',
            'annulee': '#6b7280'
        };
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusData.map(item => item.statut),
                datasets: [{
                    data: statusData.map(item => item.count),
                    backgroundColor: statusData.map(item => statusColors[item.statut] || '#e5e7eb'),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, font: { weight: '600' } } } }
            }
        });

        // Compteurs animés
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
