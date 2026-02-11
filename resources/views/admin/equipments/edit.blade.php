@extends('layouts.app')

@section('title', 'Modifier un équipement')
@section('header', 'Modifier un équipement')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
    @if(session('status'))
        <div class="mb-4 text-green-600 font-medium flex items-center p-4 bg-green-50 rounded-xl">
            <div role="alert" data-flash class="rounded-md bg-green-50 p-4">
                <i class='bx bx-check-circle mr-2 text-xl'></i>
                {{ session('status') }}
            </div>
        </div>
    @endif

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
            <h3 class="text-2xl font-bold text-gray-800">Modifier: {{ $equipment->nom }}</h3>
            <p class="text-gray-500 text-sm mt-1">Mettez à jour les informations de cet équipement.</p>
        </div>
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-orange-100 text-orange-600">
            <i class='bx bx-edit text-2xl'></i>
        </div>
    </div>

    <form method="POST" action="{{ route('equipments.update', $equipment) }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- SECTION 1: IDENTIFICATION -->
        <div>
            <div class="mb-5 text-blue-600 uppercase text-xs font-bold tracking-widest border-b border-blue-100 pb-2 flex items-center">
                <i class='bx bx-id-card mr-2 text-lg'></i> Identification
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nom -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nom de l'équipement <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class='bx bx-rename'></i>
                        </span>
                        <input type="text" name="nom" value="{{ old('nom', $equipment->nom) }}" required 
                            class="pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                </div>

                <!-- Code -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Code Unique </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class='bx bx-barcode'></i>
                        </span>
                        <input type="text" value="{{ $equipment->code }}" 
                            class="pl-10 block w-full rounded-xl border-gray-200 bg-gray-100 text-gray-600 font-mono font-bold cursor-not-allowed"
                            readonly>
                        <input type="hidden" name="code" value="{{ $equipment->code }}">
                    </div>
                    
                </div>

                <!-- Marque -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Marque</label>
                    <div class="relative">
                         <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class='bx bx-tag-alt'></i>
                        </span>
                        <input type="text" name="marque" value="{{ old('marque', $equipment->marque) }}" 
                            class="pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                    @error('marque') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Modèle -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Modèle</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class='bx bx-cube'></i>
                        </span>
                        <input type="text" name="modele" value="{{ old('modele', $equipment->modele) }}" 
                            class="pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                    @error('modele') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- (Removed redundant fields: numéro de série, années) -->
            </div>
        </div>

        <!-- SECTION 2: CLASSIFICATION (Asset Code) -->
        <div>
            <div class="mb-5 text-blue-600 uppercase text-xs font-bold tracking-widest border-b border-blue-100 pb-2 flex items-center">
                <i class='bx bx-category mr-2 text-lg'></i> Classification 
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Type d'équipement -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Type d'Équipement</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none z-10">
                            <i class='bx bx-collection'></i>
                        </span>
                        <select name="type_equipement" 
                            class="pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 transition-all appearance-none cursor-pointer">
                            <option value="" {{ old('type_equipement', $equipment->equipment_type_id) == '' ? 'selected' : '' }}>— Non défini —</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ (string)old('type_equipement', $equipment->equipment_type_id) === (string)$type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                            <i class='bx bx-chevron-down'></i>
                        </div>
                    </div>
                </div>


                <!-- (Removed redundant field: département) -->

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Localisation</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class='bx bx-buildings'></i>
                        </span>
                        <select name="site"
                            class="pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            <option value="" {{ old('site', $equipment->localisation_id) == '' ? 'selected' : '' }}>— Choisir un site —</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}" {{ (string)old('site', $equipment->localisation_id) === (string)$site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('site') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- SECTION 3: ETAT & LOCALISATION -->
        <div>
            <div class="mb-5 text-blue-600 uppercase text-xs font-bold tracking-widest border-b border-blue-100 pb-2 flex items-center">
                <i class='bx bx-map mr-2 text-lg'></i> État & Localisation
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Statut -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Statut <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class='bx bx-info-circle'></i>
                        </span>
                        <select name="statut" required
                            class="pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            <option value="actif" {{ old('statut', $equipment->statut) == 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="inactif" {{ old('statut', $equipment->statut) == 'inactif' ? 'selected' : '' }}>Inactif</option>
                        </select>
                    </div>
                    @error('statut') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- (Removed redundant field: localisation) -->

                <!-- Date d'installation -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Date d'installation</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class='bx bx-calendar-check'></i>
                        </span>
                        <input type="date" name="date_installation" value="{{ old('date_installation', $equipment->date_installation) }}" 
                            class="pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                    @error('date_installation') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Date fin garantie -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Date fin de garantie</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class='bx bx-shield'></i>
                        </span>
                        <input type="date" name="date_fin_garantie" value="{{ old('date_fin_garantie', $equipment->date_fin_garantie) }}" 
                            class="pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                    @error('date_fin_garantie') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- SECTION 4: NOTES & MEDIA -->
        <div>
            <div class="mb-5 text-blue-600 uppercase text-xs font-bold tracking-widest border-b border-blue-100 pb-2 flex items-center">
                <i class='bx bx-images mr-2 text-lg'></i> Notes & Documents
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Image -->
                <div class="p-4 border-2 border-dashed border-gray-200 rounded-xl hover:border-blue-300 transition-colors bg-gray-50/50">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Mettre à jour la photo</label>
                    @if($equipment->image_path)
                        <div class="mb-3 flex items-center justify-between bg-white p-2 rounded-lg border border-gray-100">
                             <div class="flex items-center space-x-2">
                                <img src="{{ Storage::url($equipment->image_path) }}" class="h-12 w-12 rounded object-cover shadow-sm">
                                <span class="text-xs text-gray-500 font-medium">Image actuelle</span>
                             </div>
                             <label class="flex items-center text-xs text-red-500 cursor-pointer hover:bg-red-50 p-1 px-2 rounded-lg transition">
                                <input type="checkbox" name="remove_image" class="mr-2 rounded border-red-300 text-red-500 focus:ring-red-500">
                                <i class='bx bx-trash mr-1'></i> Supprimer
                             </label>
                        </div>
                    @endif
                    <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100 cursor-pointer">
                </div>

                <!-- PDF -->
                <div class="p-4 border-2 border-dashed border-gray-200 rounded-xl hover:border-red-300 transition-colors bg-gray-50/50">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Mettre à jour le manuel</label>
                    @if($equipment->manuel_path)
                         <div class="mb-3 flex items-center justify-between bg-white p-2 rounded-lg border border-gray-100">
                            <a href="{{ Storage::url($equipment->manuel_path) }}" target="_blank" class="flex items-center text-xs text-blue-600 hover:text-blue-800 transition">
                                <i class='bx bxs-file-pdf text-xl mr-1 text-red-500'></i>
                                <span class="font-medium">Voir le manuel actuel</span>
                            </a>
                            <label class="flex items-center text-xs text-red-500 cursor-pointer hover:bg-red-50 p-1 px-2 rounded-lg transition">
                                <input type="checkbox" name="remove_manuel" class="mr-2 rounded border-red-300 text-red-500 focus:ring-red-500">
                                <i class='bx bx-trash mr-1'></i> Supprimer
                             </label>
                        </div>
                    @endif
                    <input type="file" name="manuel" accept="application/pdf" class="w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-red-50 file:text-red-700
                        hover:file:bg-red-100 cursor-pointer">
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Notes & Observations</label>
                    <textarea name="notes" rows="3"
                        class="block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 transition-all">{{ old('notes', $equipment->notes) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-end pt-6 border-t border-gray-100 space-x-4">
            <a href="{{ route('equipments.index') }}" class="px-6 py-2.5 rounded-xl text-gray-500 font-bold hover:bg-gray-100 transition">
                Annuler
            </a>
            <button type="submit" class="px-8 py-2.5 bg-[#1e293b] text-white rounded-xl font-bold shadow-lg hover:bg-gray-800 transition transform hover:-translate-y-0.5 flex items-center">
                <i class='bx bx-save mr-2'></i> Mettre à jour
            </button>
        </div>
    </form>
</div>
@endsection
