@extends('layouts.app')

@section('title', 'Tableau de bord - Employé')

@section('content')
<div class="space-y-8">
    <!-- 1. Header Section (Date on Right) -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Bonjour, {{ Auth::user()->name }} 👋</h1>
            <p class="text-gray-500 font-medium">Suivez vos demandes d'intervention et signalez de nouveaux problèmes.</p>
        </div>
        
        <div class="bg-white px-5 py-2.5 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-3 text-gray-700 w-fit shrink-0">
            <i class='bx bx-calendar text-blue-600 text-xl'></i>
            <span class="text-sm font-bold uppercase tracking-tight">{{ now()->translatedFormat('d F Y') }}</span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition group">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-600 group-hover:bg-gray-900 group-hover:text-white transition-colors">
                    <i class='bx bx-list-check text-2xl'></i>
                </div>
                <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Total</span>
            </div>
            <div class="text-3xl font-black text-gray-900">{{ $totalWorkOrders }}</div>
            <p class="text-sm text-gray-500 mt-1 font-medium">Demandes soumises</p>
        </div>

        <!-- Pending -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition group">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                    <i class='bx bx-time text-2xl'></i>
                </div>
                <span class="text-xs font-black text-indigo-400 uppercase tracking-widest">En attente</span>
            </div>
            <div class="text-3xl font-black text-gray-900">{{ $pendingWorkOrders }}</div>
            <p class="text-sm text-gray-500 mt-1 font-medium">Non encore traitées</p>
        </div>

        <!-- In Progress -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition group">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                    <i class='bx bx-wrench text-2xl'></i>
                </div>
                <span class="text-xs font-black text-amber-400 uppercase tracking-widest">En cours</span>
            </div>
            <div class="text-3xl font-black text-gray-900">{{ $inProgressWorkOrders }}</div>
            <p class="text-sm text-gray-500 mt-1 font-medium">Intervention active</p>
        </div>

        <!-- Completed -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition group">
            <div class="flex items-center justify-between mb-4">
                <div class="h-12 w-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <i class='bx bx-check-circle text-2xl'></i>
                </div>
                <span class="text-xs font-black text-emerald-400 uppercase tracking-widest">Terminées</span>
            </div>
            <div class="text-3xl font-black text-gray-900">{{ $completedWorkOrders }}</div>
            <p class="text-sm text-gray-500 mt-1 font-medium">Résolues</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Column: Graph & Recent Requests -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Annual Graph Card -->
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition duration-300">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-black text-gray-900 flex items-center gap-2">
                            <i class='bx bx-line-chart text-blue-600'></i>
                            Activité Annuelle
                        </h2>
                        <p class="text-xs text-gray-500 font-medium mt-1">Nombre de demandes soumises par mois en {{ date('Y') }}</p>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="employeeRequestsChart"></canvas>
                </div>
            </div>

            <!-- Recent Requests -->
            <div class="space-y-4">
                <div class="flex items-center justify-between px-2">
                    <h2 class="text-xl font-black text-gray-900 flex items-center gap-2">
                        <i class='bx bx-history text-blue-600'></i>
                        Vos demandes récentes
                    </h2>
                    <a href="{{ route('employee.workorders.index') }}" class="flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-100 rounded-xl text-xs font-bold text-blue-600 hover:bg-gray-50 hover:text-blue-700 transition shadow-sm">
                        <i class='bx bx-time text-sm'></i>
                        Voir historique
                    </a>
                </div>

                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Titre</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Equipement</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">Statut</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($recentWorkOrders as $workOrder)
                                    <tr class="hover:bg-gray-50/50 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $workOrder->titre }}</div>
                                            <div class="text-xs text-gray-400 truncate max-w-[200px]">{{ $workOrder->description }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <div class="h-8 w-8 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                                                    <i class='bx bx-wrench'></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-gray-700">{{ $workOrder->equipement->nom ?? 'N/A' }}</div>
                                                    <div class="text-[10px] text-gray-400 uppercase font-black tracking-tighter">{{ $workOrder->equipement->code ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex justify-center">
                                                @php
                                                    $statusClasses = [
                                                        'nouvelle' => 'bg-blue-100 text-blue-700',
                                                        'en_attente' => 'bg-indigo-100 text-indigo-700',
                                                        'en_cours' => 'bg-amber-100 text-amber-700',
                                                        'terminee' => 'bg-green-100 text-green-700',
                                                        'annulee' => 'bg-red-100 text-red-700',
                                                    ];
                                                @endphp
                                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $statusClasses[$workOrder->statut] ?? 'bg-gray-100 text-gray-600' }}">
                                                    {{ str_replace('_', ' ', $workOrder->statut) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="text-sm font-bold text-gray-900">{{ $workOrder->created_at->format('d M') }}</div>
                                            <div class="text-[10px] text-gray-400 font-medium">{{ $workOrder->created_at->format('H:i') }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center space-y-3">
                                                <div class="h-16 w-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-300">
                                                    <i class='bx bx-folder-open text-4xl'></i>
                                                </div>
                                                <p class="text-gray-400 font-medium italic">Aucune demande soumise pour le moment.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Info / Sidebar -->
        <div class="space-y-6">
            <!-- Signaler Card -->
            

            <!-- Need Help Card -->
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-8 rounded-3xl shadow-xl shadow-blue-200 text-white relative overflow-hidden group">
                <div class="relative z-10">
                    <h3 class="text-xl font-bold mb-2">Besoin d'aide ?</h3>
                    <p class="text-blue-100 text-sm mb-6 leading-relaxed">
                        Un problème technique sur votre équipement ? N'attendez pas, signalez-le immédiatement.
                    </p>
                    <a href="{{ route('employee.workorders.create') }}" class="inline-flex items-center px-6 py-3 bg-white text-blue-600 rounded-xl font-bold hover:bg-blue-50 transition gap-2">
                        Soumettre
                        
                    </a>
                </div>
                <!-- Animated Background Elements -->
                <div class="absolute top-0 right-0 -mr-8 -mt-8 h-32 w-32 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="absolute bottom-0 left-0 -ml-4 -mb-4 h-24 w-24 bg-blue-400/20 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
            </div>

            <!-- Profile Overview -->
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Votre profil</h3>
                <div class="flex items-center gap-4 mb-6">
                    <div class="h-14 w-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                        <i class='bx bxs-user-circle text-4xl'></i>
                    </div>
                    <div>
                        <div class="font-bold text-gray-900">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-tighter">Rôle</span>
                        <span class="text-xs font-black text-blue-600 bg-blue-50 px-2 py-1 rounded-md uppercase">Employé</span>
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
        const ctx = document.getElementById('employeeRequestsChart').getContext('2d');
        const monthlyData = @json($monthlyData);

        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
        gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthlyData.map(item => item.month),
                datasets: [{
                    label: 'Mes demandes',
                    data: monthlyData.map(item => item.count),
                    borderColor: '#2563eb',
                    borderWidth: 4,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2563eb',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' demande(s)';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: '600' }, color: '#9ca3af' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        ticks: { 
                            stepSize: 1,
                            font: { weight: '600' }, 
                            color: '#9ca3af' 
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
