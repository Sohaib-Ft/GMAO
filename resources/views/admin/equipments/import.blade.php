@extends('layouts.app')

@section('title', 'Importer des Équipements')
@section('header', 'Importer des Équipements')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-10 text-white relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-2xl font-bold mb-2">Importation Groupée</h3>
                <p class="text-blue-100 opacity-90">Téléchargez votre fichier CSV pour ajouter plusieurs équipements en une seule fois.</p>
            </div>
            <!-- Decorative circle -->
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white opacity-10 rounded-full"></div>
        </div>

        <form action="{{ route('equipments.import.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl">
                    <div class="flex items-center">
                        <i class='bx bx-error-circle text-xl mr-2'></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Info Guide -->
            <div class="mb-8 p-6 bg-blue-50 rounded-2xl border border-blue-100">
                <h4 class="text-blue-800 font-bold mb-3 flex items-center">
                    <i class='bx bx-info-circle mr-2 text-xl'></i> Structure du fichier attendue
                </h4>
                <p class="text-sm text-blue-700 mb-4">Votre fichier CSV doit contenir les colonnes suivantes dans cet ordre précis :</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                    <div class="bg-white p-2 rounded-lg border border-blue-200 text-center font-mono">Nom*</div>
                    <div class="bg-white p-2 rounded-lg border border-blue-200 text-center font-mono">Code*</div>
                    <div class="bg-white p-2 rounded-lg border border-blue-200 text-center font-mono">N° Série</div>
                    <div class="bg-white p-2 rounded-lg border border-blue-200 text-center font-mono">Marque</div>
                    <div class="bg-white p-2 rounded-lg border border-blue-200 text-center font-mono">Modèle</div>
                    <div class="bg-white p-2 rounded-lg border border-blue-200 text-center font-mono">Statut</div>
                    <div class="bg-white p-2 rounded-lg border border-blue-200 text-center font-mono">Localisation</div>
                </div>
                <p class="mt-4 text-xs text-blue-600 italic">* Champs obligatoires. Les statuts valides sont : actif, inactif, maintenance, panne.</p>
            </div>

            <!-- Upload Zone -->
            <div class="mb-8">
                <label for="file" class="block text-sm font-bold text-gray-700 mb-4">Choisir le fichier CSV</label>
                <div id="dropzone" class="relative group">
                    <input type="file" name="file" id="file" accept=".csv" required
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                    <div class="border-2 border-dashed border-gray-200 rounded-2xl p-10 flex flex-col items-center justify-center transition group-hover:border-blue-400 group-hover:bg-blue-50/30">
                        <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-4 transition group-hover:scale-110 group-hover:bg-blue-100">
                            <i class='bx bx-cloud-upload text-3xl text-blue-600'></i>
                        </div>
                        <p class="text-gray-600 font-medium mb-1" id="file-name">Cliquez ou glissez-déposez votre fichier ici</p>
                        <p class="text-xs text-gray-400">Format CSV uniquement (max 10 Mo)</p>
                    </div>
                </div>
                @error('file')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between border-t border-gray-100 pt-8 mt-4">
                <a href="{{ route('equipments.edit') }}" class="px-6 py-2.5 rounded-xl text-gray-500 font-bold hover:bg-gray-100 transition">
                    Annuler
                </a>
                <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition transform hover:-translate-y-0.5 flex items-center">
                    <i class='bx bx-check-circle mr-2 text-lg'></i>
                    Importer 
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('file').addEventListener('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : "Cliquez ou glissez-déposez votre fichier ici";
        document.getElementById('file-name').textContent = fileName;
        document.getElementById('file-name').classList.add('text-blue-600', 'font-bold');
    });
</script>
@endsection
