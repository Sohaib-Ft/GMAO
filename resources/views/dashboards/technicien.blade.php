@extends('layouts.app')

@section('title', 'Tableau de bord Technicien')
@section('header', 'Tableau de Bord Technicien')

@section('content')
<div class="space-y-6">
    <!-- Message de Bienvenue & Date -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
       <div class="mx-auto text-center">
    <h3 class="text-2xl font-bold text-gray-900">
        Bonjour, {{ auth()->user()->name }} 👋
    </h3>
    <p class="text-gray-500">
        Voici un résumé de vos interventions et performances.
    </p>
</div>

        <div class="flex items-center space-x-3">
          
            <div class="bg-white px-4 py-2 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-3 text-gray-700">
                <i class='bx bx-calendar text-blue-600 text-xl'></i>
                <span class="text-sm font-medium">{{ now()->translatedFormat('d F Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Grid Statistiques & Graphique -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cartes Gauche -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center space-x-4">
                <div class="h-14 w-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl">
                    <i class='bx bx-list-ul'></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">À faire</p>
                    <p class="text-2xl font-bold text-gray-900"><span data-target="{{ $stats['todo_count'] }}">0</span></p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center space-x-4">
                <div class="h-14 w-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-2xl">
                    <i class='bx bxs-alarm-exclamation'></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Urgentes</p>
                    <p class="text-2xl font-bold text-gray-900"><span data-target="{{ $stats['urgent_count'] }}">0</span></p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center space-x-4">
                <div class="h-14 w-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl">
                    <i class='bx bx-check-circle'></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Terminées</p>
                    <p class="text-2xl font-bold text-gray-900"><span data-target="{{ $stats['completed_count'] }}">0</span></p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center space-x-4">
                <div class="h-14 w-14 bg-yellow-50 text-yellow-600 rounded-2xl flex items-center justify-center text-2xl">
                    <i class='bx bx-time-five'></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Durée Moyenne</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $avgTaskDuration }}</p>
                    <p class="text-xs text-gray-500 mt-1">Temps moyen par tâche</p>
                </div>
            </div>
        </div>

        <!-- Graphique Central -->
        <div class="lg:col-span-2 bg-gradient-to-br from-white to-gray-50 p-6 rounded-3xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h4 class="text-lg font-bold text-gray-900">Mes Interventions Mensuelles</h4>
                    <p class="text-xs text-gray-500">Progression pour l'année {{ date('Y') }}</p>
                </div>
                <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center border border-indigo-100">
                    <i class='bx bx-bar-chart-alt-2 text-2xl'></i>
                </div>
            </div>
            <div class="h-56">
                <canvas id="technicianMonthlyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tableau des Tâches Assignées (Simplifié) -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <div>
                <h4 class="text-lg font-bold text-gray-900">Mes Tâches Assignées</h4>
                <p class="text-xs text-gray-500 mt-1">Liste des interventions sous votre responsabilité</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('technician.workorders.history') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-blue-600 hover:bg-blue-50 transition">
                    <i class='bx bx-history mr-2 text-lg'></i>
                    Voir l'historique
                </a>
                <a href="{{ route('technician.workorders.index') }}" class="px-4 py-1.5 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-50 transition inline-flex items-center">
                    <i class='bx bx-list-ul mr-2 text-sm'></i>
                    {{ $todoTasks->count() }} tâches en attente
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID / Priorité</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Équipement</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Assigné le</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($todoTasks as $task)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-gray-400">#{{ $task->id }}</span>
                                @php
                                    $priorityClasses = [
                                        'basse' => 'text-green-600 bg-green-50',
                                        'normale' => 'text-blue-600 bg-blue-50',
                                        'haute' => 'text-orange-600 bg-orange-50',
                                        'urgente' => 'text-red-600 bg-red-50',
                                    ];
                                @endphp
                                <span class="mt-1 px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase w-fit {{ $priorityClasses[$task->priorite] ?? 'bg-gray-50 text-gray-500' }}">
                                    {{ $task->priorite }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-900">{{ $task->equipement->nom ?? 'Inconnu' }}</span>
                                <span class="text-[10px] text-gray-400 uppercase tracking-tighter">{{ $task->equipement->code_actif ?? 'SANS CODE' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600 line-clamp-1 max-w-xs">{{ $task->description }}</p>
                        </td>
                       
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                            {{ $task->created_at->translatedFormat('d M H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            <i class='bx bx-task-x text-4xl mb-2 opacity-20'></i>
                            <p class="text-sm font-medium">Aucune tâche assignée pour le moment.</p>
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
    // Graphique Mensuel Technicien
    const ctx = document.getElementById('technicianMonthlyChart').getContext('2d');
    const monthlyData = @json($monthlyInterventions);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'Interventions Terminées',
                data: monthlyData.map(item => item.count),
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#4f46e5',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: 10,
                    ticks: { stepSize: 1, color: '#94a3b8' },
                    grid: { color: 'rgba(0,0,0,0.03)' }
                },
                x: {
                    ticks: { color: '#94a3b8' },
                    grid: { display: false }
                }
            }
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
            const value = Math.floor(progress * target);
            counter.textContent = value;
            if (progress < 1) requestAnimationFrame(update);
            else counter.textContent = target;
        }
        requestAnimationFrame(update);
    });
</script>
@endpush
@endsection