@props(['workOrders', 'role' => 'admin'])

<div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest leading-none">Équipement</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest leading-none">Détails</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest leading-none">Priorité</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest leading-none">Statut</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest leading-none text-right">  </th>
                    @if($role === 'technician_history')
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest leading-none text-right">Durée</th>
                    @else

                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($workOrders as $order)
                    <tr class="hover:bg-blue-50/20 transition-colors group">
                        
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $order->equipement->nom ?? '—' }}</span>
                                <span class="text-[10px] text-gray-400 font-mono tracking-tighter uppercase">{{ $order->equipement->code ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-semibold text-gray-800 line-clamp-1">{{ $order->titre }}</span>
                                <span class="text-xs text-gray-400 italic">
                                    {{ $role === 'technician_history' ? 'Terminé le ' . $order->updated_at->format('d/m/Y') : $order->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <x-work-orders.priority-badge :priority="$order->priorite" />
                        </td>
                        <td class="px-6 py-4">
                            <x-work-orders.status-badge :status="$order->statut" />
                        </td>

                        <td class="px-6 py-4 text-right">
                            @if($role === 'technician_history')
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-xs font-bold border border-emerald-100 italic">
                                    @if($order->duree)
                                        @if($order->duree < 1440)
                                            {{ $order->duree . ' min' }}
                                        @else
                                            {{ round($order->duree / 1440, 2) . ' jours' }}
                                        @endif
                                    @else
                                        -
                                    @endif
                                </span>
                            @else
                                <div class="flex items-center justify-end space-x-2">
                                    @if($role === 'admin')
                                        <a href="{{ route('admin.workorders.show', $order) }}" class="p-2 text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Détails">
                                            <i class='bx bx-show text-xl'></i>
                                        </a>
                                        <a href="{{ route('admin.workorders.edit', $order) }}" class="p-2 text-blue-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Modifier">
                                            <i class='bx bx-edit-alt text-xl'></i>
                                        </a>
                                    @elseif($role === 'technician')
                                        @if($order->technicien_id === auth()->id())
                                            <div class="flex items-center gap-2">
                                                @if($order->statut === 'en_attente')
                                                    <form action="{{ route('technician.workorders.start', $order) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-100 flex items-center gap-2">
                                                            <i class='bx bx-play-circle'></i>
                                                            Démarrer
                                                        </button>
                                                    </form>
                                                @elseif($order->statut === 'en_cours')
                                                    <form action="{{ route('technician.workorders.complete', $order) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-xl text-xs font-bold hover:bg-green-700 transition shadow-lg shadow-green-100 flex items-center gap-2">
                                                            <i class='bx bx-check-circle'></i>
                                                            Terminer
                                                        </button>
                                                    </form>
                                                    <button type="button" onclick="openIrreparableModal({{ $order->id }}, '{{ addslashes($order->equipement->nom ?? 'cet équipement') }}')" 
                                                            class="px-4 py-2 bg-red-100 text-red-600 rounded-xl text-xs font-bold hover:bg-red-600 hover:text-white transition flex items-center gap-1">
                                                        <i class='bx bx-x-circle'></i>
                                                        irréparable
                                                    </button>
                                                @endif
                                            </div>
                                        @elseif($order->technicien_id === null)
                                            <form action="{{ route('technician.workorders.assign', $order) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700 transition shadow-lg shadow-emerald-100 flex items-center gap-2">
                                                    <i class='bx bx-user-plus'></i>
                                                    S'assigner
                                                </button>
                                            </form>
                                        @else
                                            <button disabled class="px-4 py-2 bg-gray-100 text-gray-400 rounded-xl text-xs font-bold border border-gray-200 cursor-not-allowed flex items-center gap-2">
                                                <i class='bx bx-lock-alt'></i>
                                                Déjà assigné
                                            </button>
                                        @endif
                                    @elseif($role === 'technician_available')
                                        <form action="{{ route('technician.workorders.assign', $order) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700 transition shadow-lg shadow-emerald-100 flex items-center gap-2">
                                                <i class='bx bx-user-plus'></i>
                                                S'assigner
                                            </button>
                                        </form>
                                    @elseif($role === 'employee')
                                        <div class="flex items-center justify-end space-x-2">
                                            <button type="button" onclick="openDeleteModal('{{ route('employee.workorders.destroy', $order) }}', '{{ addslashes($order->titre) }}')" 
                                                class="p-2.5 bg-red-50 text-red-400 rounded-xl hover:bg-red-600 hover:text-white transition-all inline-flex items-center justify-center shadow-sm" 
                                                title="Supprimer">
                                                <i class='bx bx-trash text-xl'></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class='bx bx-list-minus text-5xl text-gray-200 mb-4'></i>
                                <p class="text-lg font-medium text-gray-900">Aucun ordre de travail</p>
                                <p class="text-sm text-gray-500">Il n'y a pas de données à afficher pour le moment.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($workOrders->hasPages())
    <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50">
        {{ $workOrders->appends(request()->query())->links() }}
    </div>
    @endif
</div>
