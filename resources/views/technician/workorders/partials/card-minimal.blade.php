<div class="bg-white rounded-3xl shadow-lg border border-gray-100 hover:shadow-xl transition-all p-6 mb-4">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <!-- Titre et Statut -->
        <div class="flex-1 flex items-center gap-3">
            @if($order->statut === 'en_attente')
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200 uppercase tracking-wide">
                    Assigné
                </span>
            @elseif($order->statut === 'en_cours')
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200 uppercase tracking-wide flex items-center gap-1">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                    En cours
                </span>
            @endif
            <h3 class="text-lg font-bold text-gray-900">{{ $order->titre }}</h3>
        </div>

        <!-- Actions Buttons -->
        <div class="flex flex-wrap items-center justify-end gap-3 w-full md:w-auto">
            @if($order->statut === 'en_attente')
                <!-- Démarrer -->
                <form action="{{ route('technician.workorders.start', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="whitespace-nowrap px-5 py-2.5 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition shadow-md shadow-blue-200 flex items-center gap-2 text-sm">
                        <i class='bx bx-play-circle text-lg'></i>
                        Démarrer
                    </button>
                </form>
            @endif

            @if($order->statut === 'en_cours')
                <!-- Terminer -->
                <form action="{{ route('technician.workorders.complete', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="whitespace-nowrap px-5 py-2.5 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 transition shadow-md shadow-green-200 flex items-center gap-2 text-sm">
                        <i class='bx bx-check-circle text-lg'></i>
                        Terminé
                    </button>
                </form>

                <!-- Irréparable -->
                <button type="button" onclick="openIrreparableModal({{ $order->id }}, '{{ addslashes($order->equipement->nom ?? 'cet équipement') }}')" 
                        class="whitespace-nowrap px-5 py-2.5 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition shadow-md shadow-red-200 flex items-center gap-2 text-sm">
                    <i class='bx bx-x-circle text-lg'></i>
                    Irréparable
                </button>
            @endif

            <!-- Détails -->
            <a href="{{ route('technician.workorders.show', $order) }}" 
               class="whitespace-nowrap px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition flex items-center gap-2 text-sm">
                <i class='bx bx-detail text-lg'></i>
                Détails
            </a>
        </div>
    </div>
</div>
