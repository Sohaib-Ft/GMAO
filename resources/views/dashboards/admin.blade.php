@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('header', 'Tableau de Bord Administrateur')

@section('content')
<div class="space-y-6">
    <!-- Grid Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Utilisateurs -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="h-14 w-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                <i class='bx bx-group text-2xl'></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Utilisateurs</p>
                <p class="text-2xl font-bold text-gray-900"><span id="totalUsersCounter" data-target="{{ $stats['total_users'] }}">0</span></p>
            </div>
        </div>

        <!-- Techniciens -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="h-14 w-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center">
                <i class='bx bx-wrench text-2xl'></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Techniciens</p>
                <p class="text-2xl font-bold text-gray-900"><span id="techniciansCounter" data-target="{{ $stats['technicians_count'] }}">0</span></p>
            </div>
        </div>

        <!-- Utilisateurs Standards -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="h-14 w-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center">
                <i class='bx bx-user text-2xl'></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Utilisateurs Standards</p>
                <p class="text-2xl font-bold text-gray-900"><span id="standardUsersCounter" data-target="{{ $stats['standard_users_count'] }}">0</span></p>
            </div>
        </div>

        <!-- Total Équipements -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="h-14 w-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center">
                <i class='bx bx-box text-2xl'></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Équipements</p>
                <p class="text-2xl font-bold text-gray-900"><span id="equipmentsCounter" data-target="{{ $stats['equipments_count'] }}">0</span></p>
            </div>
        </div>

        <!-- Top Technicien -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="h-14 w-14 bg-sky-50 text-sky-600 rounded-2xl flex items-center justify-center">
                <i class='bx bx-user-check text-2xl'></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Top Technicien</p>
                <p class="text-lg font-semibold text-gray-900">{{ $stats['top_technician']->name ?? 'N/A' }}</p>
                <p class="text-2xl font-bold text-gray-900"><span id="topTechnicianCounter" data-target="{{ $stats['top_technician']->technicien_work_orders_count ?? 0 }}">0</span> <span class="text-sm font-normal text-gray-500">interventions</span></p>
            </div>
        </div>

        <!-- Top Équipement -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="h-14 w-14 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center">
                <i class='bx bxs-hot text-2xl'></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Équipement le plus Réclamé</p>
                <p class="text-lg font-semibold text-gray-900">{{ $stats['most_claimed_equipment']->nom ?? 'N/A' }}</p>
                <p class="text-2xl font-bold text-gray-900"><span id="topEquipmentCounter" data-target="{{ $stats['most_claimed_equipment']->work_orders_count ?? 0 }}">0</span> <span class="text-sm font-normal text-gray-500">demandes</span></p>
            </div>
        </div>

        <!-- Top Demandeur -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="h-14 w-14 bg-fuchsia-50 text-fuchsia-600 rounded-2xl flex items-center justify-center">
                <i class='bx bxs-megaphone text-2xl'></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Top Demandeur</p>
                <p class="text-lg font-semibold text-gray-900">{{ $stats['top_claimant']->name ?? 'N/A' }}</p>
                <p class="text-2xl font-bold text-gray-900"><span id="topClaimantCounter" data-target="{{ $stats['top_claimant']->employe_work_orders_count ?? 0 }}">0</span> <span class="text-sm font-normal text-gray-500">demandes</span></p>
            </div>
        </div>
    </div>

    <!-- Alertes de Maintenance -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Demandes en Attente -->
        <div class="bg-gradient-to-br from-orange-50 to-white p-6 rounded-3xl shadow-sm border border-orange-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-orange-600 mb-1">Demandes en Attente</p>
                    <p class="text-3xl font-bold text-gray-900"><span id="pendingRequestsCounter" data-target="{{ $stats['pending_requests'] }}">0</span></p>
                    <p class="text-xs text-gray-500 mt-1">Nouvelles tâches non traitées</p>
                </div>
                <div class="h-16 w-16 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center">
                    <i class='bx bx-time-five text-3xl'></i>
                </div>
            </div>
        </div>

        <!-- Tâches en Cours -->
        <div class="bg-gradient-to-br from-blue-50 to-white p-6 rounded-3xl shadow-sm border border-blue-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600 mb-1">Tâches en Cours</p>
                    <p class="text-3xl font-bold text-gray-900"><span id="tasksInProgressCounter" data-target="{{ $stats['tasks_in_progress'] }}">0</span></p>
                    <p class="text-xs text-gray-500 mt-1">Interventions actives</p>
                </div>
                <div class="h-16 w-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center">
                    <i class='bx bx-loader-alt text-3xl'></i>
                </div>
            </div>
        </div>

        <!-- Pannes Critiques -->
        <div class="bg-gradient-to-br from-red-50 to-white p-6 rounded-3xl shadow-sm border border-red-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-red-600 mb-1">Pannes Critiques</p>
                    <p class="text-3xl font-bold text-gray-900"><span id="criticalFailuresCounter" data-target="{{ $stats['critical_failures'] }}">0</span></p>
                    <p class="text-xs text-gray-500 mt-1">Priorité urgente</p>
                </div>
                <div class="h-16 w-16 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center">
                    <i class='bx bx-error text-3xl'></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques de Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Interventions par Mois -->
        <div class="bg-gradient-to-br from-blue-50 via-white to-indigo-50 p-6 rounded-3xl shadow-lg border border-blue-100 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Interventions par Mois</h3>
                    <p class="text-xs text-gray-500 mt-1">Performance mensuelle</p>
                </div>
                <div class="h-12 w-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center">
                    <i class='bx bx-line-chart text-2xl'></i>
                </div>
            </div>
            <div class="h-64 bg-white/50 rounded-2xl p-4">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- Répartition des Statuts -->
        <div class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 p-6 rounded-3xl shadow-lg border border-indigo-100 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Répartition des Statuts</h3>
                    <p class="text-xs text-gray-500 mt-1">Distribution des tâches</p>
                </div>
                <div class="h-12 w-12 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center">
                    <i class='bx bx-pie-chart-alt-2 text-2xl'></i>
                </div>
            </div>
            <div class="h-64 bg-white/50 rounded-2xl p-4">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tableau des Demandes Récentes -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Demandes Récentes</h3>
                <span class="text-sm text-gray-500">5 dernières interventions</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Équipement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Technicien</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priorité</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($recentRequests as $request)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-8 w-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-medium">
                                    {{ substr(strtoupper($request->employe->name ?? 'N/A'), 0, 2) }}
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $request->employe->name ?? 'Non assigné' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-900">{{ $request->equipement->nom ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $request->equipement->code_actif ?? '' }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-900">{{ $request->technicien->name ?? 'Non assigné' }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $priorityClasses = [
                                    'basse' => 'bg-green-100 text-green-800',
                                    'moyenne' => 'bg-yellow-100 text-yellow-800',
                                    'haute' => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $priorityClasses[$request->priorite] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($request->priorite) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClasses = [
                                    'nouvelle' => 'bg-blue-100 text-blue-800',
                                    'affectee' => 'bg-purple-100 text-purple-800',
                                    'en_cours' => 'bg-indigo-100 text-indigo-800',
                                    'terminee' => 'bg-green-100 text-green-800',
                                    'annulee' => 'bg-gray-100 text-gray-800',
                                ];
                            @endphp
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$request->statut] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst(str_replace('_', ' ', $request->statut)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $request->created_at->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <i class='bx bx-info-circle text-3xl mb-2'></i>
                            <p>Aucune demande récente</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Configuration commune
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
            }
        }
    };

    // Graphique des Interventions Mensuelles
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyData = @json($monthlyInterventions);
    
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'Interventions Terminées',
                data: monthlyData.map(item => item.count),
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2,
                borderRadius: 8,
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Graphique de Répartition des Statuts
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusData = @json($statusDistribution);
    
    const statusLabels = {
        'nouvelle': 'Nouvelle',
        'affectee': 'Affectée',
        'en_cours': 'En Cours',
        'terminee': 'Terminée',
        'annulee': 'Annulée'
    };
    
    const statusColors = {
        'nouvelle': 'rgba(59, 130, 246, 0.8)',
        'affectee': 'rgba(168, 85, 247, 0.8)',
        'en_cours': 'rgba(99, 102, 241, 0.8)',
        'terminee': 'rgba(16, 185, 129, 0.8)',
        'annulee': 'rgba(107, 114, 128, 0.8)'
    };
    
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusData.map(item => statusLabels[item.statut] || item.statut),
            datasets: [{
                data: statusData.map(item => item.count),
                backgroundColor: statusData.map(item => statusColors[item.statut] || 'rgba(156, 163, 175, 0.8)'),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            ...commonOptions,
            cutout: '60%',
        }
    });

    // Compteurs animés pour toutes les cartes
    const counters = document.querySelectorAll('[data-target]');
    console.log('Compteurs trouvés:', counters.length);
    
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target')) || 0;
        console.log('Compteur ID:', counter.id, 'Target:', target);
        const duration = 800; // durée en ms
        const startTime = performance.now();

        function update(now) {
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const value = Math.floor(progress * target);
            counter.textContent = value;
            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                counter.textContent = target;
            }
        }

        requestAnimationFrame(update);
    });
</script>
@endpush
@endsection
