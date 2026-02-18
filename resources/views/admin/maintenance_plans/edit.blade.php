@extends('layouts.app')

@section('title', 'Modifier un Plan de Maintenance')
@section('header', 'Modifier le Plan de Maintenance')

@push('styles')
<style>
    .autocomplete-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 2px solid #3b82f6;
        border-radius: 0.75rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        max-height: 250px;
        overflow-y: auto;
        overflow-x: hidden;
        z-index: 50;
        margin-top: 8px;
    }
    .autocomplete-item {
        padding: 14px 16px;
        cursor: pointer;
        transition: all 0.2s;
        background: #fafafa;
        border-bottom: 1px solid #f0f0f0;
    }
    .autocomplete-item:hover {
        background-color: #f0f7ff;
    }
    .autocomplete-item:last-child {
        border-bottom: none;
    }
    .autocomplete-item.selected {
        background-color: #e0f2fe;
    }
    #equipment-search {
        border: 2px solid #3b82f6 !important;
    }
    #equipment-search:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
    @if($errors->any())
        <div class="mb-4 text-red-600 font-medium p-4 bg-red-50 rounded-xl">
            <div class="flex items-center mb-2">
                <i class='bx bx-error-circle mr-2 text-xl'></i>
                <strong>Erreurs de validation :</strong>
            </div>
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-8 flex items-center justify-between border-b border-gray-100 pb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Modifier le Plan</h3>
            <p class="text-gray-500 text-sm mt-1">{{ $maintenancePlan->equipement->code }} - {{ $maintenancePlan->equipement->nom }}</p>
        </div>
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-blue-100 text-blue-600">
            <i class='bx bx-edit text-2xl'></i>
        </div>
    </div>

    <form method="POST" action="{{ route('maintenance-plans.update', $maintenancePlan) }}" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- SECTION 1: ÉQUIPEMENT -->
        <div>
            <div class="mb-5 text-blue-600 uppercase text-xs font-bold tracking-widest border-b border-blue-100 pb-2 flex items-center">
                <i class='bx bx-wrench mr-2 text-lg'></i> Équipement
            </div>
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Equipment Selection with Autocomplete -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Équipement <span class="text-red-500">*</span>
                    </label>
                    <div class="relative" id="equipment-autocomplete-container">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 z-10">
                            <i class='bx bx-search'></i>
                        </span>
                        <input type="text" 
                               id="equipment-search" 
                               placeholder="Rechercher un équipement..." 
                               autocomplete="off"
                               class="pl-10 w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                        <input type="hidden" name="equipement_id" id="equipement_id" value="{{ old('equipement_id', $maintenancePlan->equipement_id) }}" required>
                        
                        <!-- Autocomplete Dropdown -->
                        <div id="autocomplete-dropdown" class="autocomplete-dropdown hidden"></div>
                    </div>
                    @error('equipement_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- SECTION 2: RÉCURRENCE RRULE -->
        <div>
            <div class="mb-5 text-blue-600 uppercase text-xs font-bold tracking-widest border-b border-blue-100 pb-2 flex items-center">
                <i class='bx bx-sync mr-2 text-lg'></i> Récurrence
            </div>
            
            <x-rrule-generator 
                name="rrule" 
                :value="old('rrule', $maintenancePlan->rrule)"
                label="Règle de Récurrence (RRULE)"
                :required="true"
            />

            @error('rrule') 
                <p class="text-red-600 text-xs mt-2"><i class='bx bx-error-circle'></i> {{ $message }}</p> 
            @enderror
        </div>

        <!-- SECTION 3: CONFIGURATION SUPPLÉMENTAIRE -->
        <div>
            <div class="mb-5 text-blue-600 uppercase text-xs font-bold tracking-widest border-b border-blue-100 pb-2 flex items-center">
                <i class='bx bx-cog mr-2 text-lg'></i> Configuration Supplémentaire
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Type (Hidden, always Preventive as requested) -->
                <input type="hidden" name="type" value="preventive">

                <!-- Technicien Assigné -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Technicien Assigné
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class='bx bx-user'></i>
                        </span>
                        <select name="technicien_id" 
                            class="pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">— Aucun technicien (Non assigné) —</option>
                            @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}" {{ old('technicien_id', $maintenancePlan->technicien_id) == $tech->id ? 'selected' : '' }}>
                                    {{ $tech->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('technicien_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Hidden Statut -->
                <input type="hidden" name="statut" value="{{ $maintenancePlan->statut }}">
            </div>
        </div>

        <!-- SECTION 3: DATES -->
        <div>
            <div class="mb-5 text-blue-600 uppercase text-xs font-bold tracking-widest border-b border-blue-100 pb-2 flex items-center">
                <i class='bx bx-calendar-event mr-2 text-lg'></i> Planification
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Derniere date -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Dernière Maintenance Effectuée
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class='bx bx-calendar-check'></i>
                        </span>
                        <input type="date" name="derniere_date" 
                            value="{{ old('derniere_date', $maintenancePlan->derniere_date?->format('Y-m-d')) }}" 
                            class="pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    @error('derniere_date') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Prochaine date -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Prochaine Maintenance Prévue
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class='bx bx-calendar-plus'></i>
                        </span>
                        <input type="date" name="prochaine_date" 
                            value="{{ old('prochaine_date', $maintenancePlan->prochaine_date?->format('Y-m-d')) }}" 
                            class="pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    @error('prochaine_date') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-end pt-6 border-t border-gray-100 space-x-4">
            <a href="{{ route('maintenance-plans.index') }}" class="px-6 py-2.5 rounded-xl text-gray-500 font-bold hover:bg-gray-100 transition">
                Annuler
            </a>

            <button type="submit" class="px-8 py-2.5 bg-[#1e293b] text-white rounded-xl font-bold shadow-lg hover:bg-gray-800 transition transform hover:-translate-y-0.5 flex items-center">
                <i class='bx bx-save mr-2'></i> Enregistrer
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('equipment-search');
    const hiddenInput = document.getElementById('equipement_id');
    const dropdown = document.getElementById('autocomplete-dropdown');
    let debounceTimer;
    let selectedIndex = -1;
    let equipmentData = [];
    let allEquipments = [];

    // Pre-fill current equipment
    const currentEquipmentId = '{{ $maintenancePlan->equipement_id }}';
    if (currentEquipmentId) {
        fetch(`/api/equipments/${currentEquipmentId}`)
            .then(response => response.json())
            .then(equipment => {
                searchInput.value = `${equipment.nom}`;
                hiddenInput.value = equipment.id;
            })
            .catch(error => console.error('Error loading equipment:', error));
    }

    // Load all equipments on focus/click
    searchInput.addEventListener('focus', function() {
        if (equipmentData.length === 0) {
            loadAllEquipments();
        } else {
            dropdown.classList.remove('hidden');
        }
    });

    searchInput.addEventListener('click', function() {
        if (equipmentData.length === 0) {
            loadAllEquipments();
        } else {
            dropdown.classList.remove('hidden');
        }
    });

    function loadAllEquipments() {
        fetch('/api/equipments/search?q=')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                allEquipments = data;
                equipmentData = data;
                renderDropdown(data);
            })
            .catch(error => {
                console.error('Error loading equipments:', error);
                dropdown.innerHTML = '<div class="autocomplete-item text-red-500 text-sm">Erreur de chargement</div>';
                dropdown.classList.remove('hidden');
            });
    }

    // Search equipment via AJAX
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(debounceTimer);
        
        if (query.length === 0) {
            equipmentData = allEquipments;
            renderDropdown(allEquipments);
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`/api/equipments/search?q=${encodeURIComponent(query)}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    equipmentData = data;
                    renderDropdown(data);
                })
                .catch(error => {
                    console.error('Error fetching equipment:', error);
                });
        }, 300);
    });

    function renderDropdown(items) {
        if (items.length === 0) {
            dropdown.innerHTML = '<div class="autocomplete-item text-gray-500 text-sm text-center">Aucun équipement trouvé</div>';
            dropdown.classList.remove('hidden');
            return;
        }

        dropdown.innerHTML = items.map((item, index) => `
            <div class="autocomplete-item" data-index="${index}" data-id="${item.id}">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center mr-3 flex-shrink-0">
                        <i class='bx bx-wrench text-blue-600 text-xl'></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-bold text-gray-900 text-base">${item.nom}</div>
                        <div class="text-xs text-gray-500">${item.code}</div>
                        <div class="text-xs text-gray-400 italic">${item.localisation || 'N/A'}</div>
                    </div>
                </div>
            </div>
        `).join('');

        dropdown.classList.remove('hidden');
        selectedIndex = -1;

        dropdown.querySelectorAll('.autocomplete-item').forEach((item, idx) => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                selectEquipment(idx);
            });
        });
    }

    function selectEquipment(index) {
        if (index >= 0 && index < equipmentData.length) {
            const selected = equipmentData[index];
            searchInput.value = `${selected.nom}`;
            hiddenInput.value = selected.id;
            dropdown.classList.add('hidden');
            selectedIndex = -1;
        }
    }

    searchInput.addEventListener('keydown', function(e) {
        const items = dropdown.querySelectorAll('.autocomplete-item');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
            updateSelection(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, 0);
            updateSelection(items);
        } else if (e.key === 'Enter' && selectedIndex >= 0) {
            e.preventDefault();
            selectEquipment(selectedIndex);
        } else if (e.key === 'Escape') {
            dropdown.classList.add('hidden');
        }
    });

    function updateSelection(items) {
        items.forEach((item, index) => {
            if (index === selectedIndex) {
                item.classList.add('selected');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('selected');
            }
        });
    }

    document.addEventListener('click', function(e) {
        const container = document.getElementById('equipment-autocomplete-container');
        if (container && !container.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
});
</script>
@endpush
