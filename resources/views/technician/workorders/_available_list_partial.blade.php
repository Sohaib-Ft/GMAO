<div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
    <div class="p-6 border-b border-gray-50 bg-gray-50/50 flex flex-wrap justify-between items-center gap-4">
        <h3 class="font-bold text-gray-800 flex items-center">
            <i class='bx bx-search-alt mr-2 text-blue-500'></i>
            {{ $workOrders->total() }} ordres trouvés
        </h3>
    </div>

    <div class="divide-y divide-gray-50">
        @forelse($workOrders as $order)
        <div class="p-8 hover:bg-gray-50/80 transition group">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-3 mb-3">
                        @php
                            $priorityClasses = [
                                'basse' => 'bg-gray-100 text-gray-500',
                                'normale' => 'bg-blue-100 text-blue-600',
                                'haute' => 'bg-orange-100 text-orange-600',
                                'urgente' => 'bg-red-100 text-red-600',
                            ];
                            $statusClasses = [
                                'en_attente' => 'bg-blue-50 text-blue-500',
                                'en_cours' => 'bg-amber-50 text-amber-500 border-amber-100',
                                'terminee' => 'bg-green-50 text-green-500',
                            ];
                        @endphp
                        <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest {{ $priorityClasses[$order->priorite] ?? 'bg-gray-100' }}">
                            {{ $order->priorite }}
                        </span>
                        <span class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase {{ $statusClasses[$order->statut] ?? 'bg-gray-50' }}">
                            {{ str_replace('_', ' ', $order->statut) }}
                        </span>
                        <span class="text-xs text-gray-400 font-medium">#{{ $order->id }}</span>
                    </div>
                    
                    <h4 class="text-xl font-bold text-gray-900 mb-2 truncate max-w-2xl">{{ $order->titre }}</h4>
                    <p class="text-gray-500 text-sm line-clamp-2 italic mb-4">"{{ $order->description }}"</p>

                    <div class="flex flex-wrap items-center gap-6 text-sm">
                        <div class="flex items-center text-gray-700 font-semibold">
                            <i class='bx bx-wrench mr-2 text-blue-500'></i>
                            {{ $order->equipement->nom ?? 'N/A' }}
                            <span class="ml-2 px-2 py-0.5 bg-gray-100 text-[10px] text-gray-400 rounded uppercase font-mono">{{ $order->equipement->code ?? '' }}</span>
                        </div>
                        <div class="flex items-center text-gray-500">
                            <i class='bx bx-map-pin mr-2 text-gray-400'></i>
                            {{ $order->equipement->site ?? 'Non spécifié' }}
                        </div>
                        
                        @if($order->technicien_id)
                            <div class="flex items-center text-indigo-600 font-bold">
                                <i class='bx bx-user-check mr-2'></i>
                                Affecté à : {{ $order->technicien->name }}
                            </div>
                        @else
                            <div class="flex items-center text-emerald-600 font-bold italic">
                                <i class='bx bx-user-plus mr-2'></i>
                                Non assigné (Libre)
                            </div>
                        @endif
                    </div>
                </div>

                <div class="w-full lg:w-auto">
                    @if(!$order->technicien_id)
                        <form action="{{ route('technician.workorders.assign', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full lg:w-auto px-8 py-4 bg-gray-900 text-white rounded-2xl font-bold hover:bg-blue-600 transition transform hover:-translate-y-1 shadow-lg flex items-center justify-center gap-2 group-hover:bg-blue-600">
                                <i class='bx bx-user-plus text-xl'></i>
                                S'assigner la tâche
                            </button>
                        </form>
                    @else
                        <button disabled class="w-full lg:w-auto px-8 py-4 bg-gray-100 text-gray-400 rounded-2xl font-bold cursor-not-allowed flex items-center justify-center gap-2">
                            <i class='bx bx-lock-alt text-xl'></i>
                            Déjà assigné
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="py-24 text-center">
            <div class="h-24 w-24 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6 transform rotate-12">
                <i class='bx bx-search text-5xl text-blue-300'></i>
            </div>
            <h5 class="text-2xl font-black text-gray-900 mb-2">Aucun résultat</h5>
            <p class="text-gray-500 max-w-sm mx-auto">Veuillez modifier vos filtres pour voir d'autres ordres de travail.</p>
        </div>
        @endforelse
    </div>

    @if($workOrders->hasPages())
    <div class="p-8 bg-gray-50 border-t border-gray-100">
        {{ $workOrders->links() }}
    </div>
    @endif
</div>
