@extends('layouts.app')

@section('title', 'Mon Profil')

@section('header', 'Mon Profil')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-12">
    
    <!-- Success Message -->
    @if (session('status') === 'profile-updated' || session('status') === 'password-updated')
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-xl shadow-sm mb-6 flex items-center" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
            <i class='bx bxs-check-circle mr-3 text-xl'></i>
            <span>{{ session('status') === 'profile-updated' ? 'Profil mis à jour avec succès.' : 'Mot de passe mis à jour avec succès.' }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Summary & Info -->
        <div class="lg:col-span-1 space-y-8">
            <!-- Profile Card (Premium Design) -->
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 group/profile">
                <!-- Banner / Header Sombre -->
                <div class="h-32 bg-[#1e293b] relative"></div>
                
                <!-- Avatar Section -->
                <div class="px-6 pb-8 text-center -mt-16 relative">
                    <div class="relative inline-block">
                        <div class="h-32 w-32 rounded-full border-4 border-white shadow-lg overflow-hidden bg-blue-50 mx-auto transition-transform group-hover/profile:scale-105 duration-500">
                            <img src="{{ $user->profile_photo_url }}" 
                                 alt="{{ $user->name }}"
                                 class="h-full w-full object-cover"
                                 id="profile-preview">
                        </div>
                        
                        <!-- Camera Upload Button -->
                        <form id="photo-upload-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            <input type="file" name="photo" id="photo-input" class="hidden" accept="image/*" onchange="previewAndSubmit(this)">
                            
                            <button type="button" 
                                    onclick="document.getElementById('photo-input').click()"
                                    class="absolute bottom-0 right-0 h-10 w-10 bg-blue-600 text-white rounded-full border-4 border-white flex items-center justify-center hover:bg-blue-700 transition shadow-md hover:scale-110 active:scale-95">
                                <i class='bx bxs-camera text-lg'></i>
                            </button>
                        </form>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-xl font-black text-gray-900 leading-tight uppercase">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500 font-medium">{{ $user->email }}</p>
                    </div>

                    <div class="mt-4 flex justify-center">
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black bg-blue-50 text-blue-600 uppercase tracking-widest border border-blue-100">
                           <i class='bx bxs-shield-alt mr-1.5'></i> {{ $user->role }}
                        </span>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-50 flex items-center justify-between text-[11px]">
                        <span class="text-gray-400 font-bold uppercase tracking-widest">Membre depuis :</span>
                        <span class="text-gray-900 font-black">{{ $user->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Role Permissions Note -->
        
            <!-- My Work Orders / Commits Card -->
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-blue-600 rounded-xl text-white">
                            <i class='bx bx-list-check text-2xl'></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-800">Mes Engagements</h4>
                            <p class="text-xs text-gray-600">
                                @if ($user->isEmployee())
                                    Demandes d'intervention
                                @elseif ($user->isTechnician())
                                    Interventions assignées
                                @else
                                    Ordres de travail récents
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @if($workOrders->isEmpty())
                        <div class="text-center py-8">
                            <i class='bx bx-folder-open text-6xl text-gray-300 mb-3'></i>
                            <p class="text-gray-500 text-sm">Aucun ordre de travail pour le moment.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($workOrders as $workOrder)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition group">
                                    <div class="flex items-start space-x-3 flex-1">
                                        <div class="mt-1">
                                            @if($workOrder->statut === 'terminee')
                                                <i class='bx bxs-check-circle text-green-500 text-xl'></i>
                                            @elseif($workOrder->statut === 'en_cours')
                                                <i class='bx bxs-time-five text-blue-500 text-xl'></i>
                                            @elseif($workOrder->statut === 'echec')
                                                <i class='bx bxs-x-circle text-red-500 text-xl'></i>
                                            @else
                                                <i class='bx bxs-info-circle text-gray-400 text-xl'></i>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-gray-800 truncate">{{ $workOrder->titre }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ $workOrder->equipement->nom ?? 'N/A' }}</p>
                                            <div class="flex items-center space-x-2 mt-1">
                                                @if($workOrder->priorite === 'urgente')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-700">
                                                        <i class='bx bxs-error text-xs mr-1'></i>Urgente
                                                    </span>
                                                @elseif($workOrder->priorite === 'haute')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-orange-100 text-orange-700">
                                                        Haute
                                                    </span>
                                                @elseif($workOrder->priorite === 'normale')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-700">
                                                        Normale
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-gray-100 text-gray-700">
                                                        Basse
                                                    </span>
                                                @endif
                                                <span class="text-xs text-gray-400">{{ $workOrder->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="
                                        @if ($user->isEmployee())
                                            {{ route('employee.workorders.index') }}
                                        @elseif ($user->isTechnician())
                                            {{ route('technician.workorders.show', $workOrder) }}
                                        @else
                                            {{ route('admin.workorders.show', $workOrder) }}
                                        @endif
                                    " class="opacity-0 group-hover:opacity-100 transition p-2 hover:bg-blue-100 rounded-lg">
                                        <i class='bx bx-right-arrow-alt text-blue-600 text-xl'></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <a href="
                                @if ($user->isEmployee())
                                    {{ route('employee.workorders.index') }}
                                @elseif ($user->isTechnician())
                                    {{ route('technician.workorders.index') }}
                                @else
                                    {{ route('admin.workorders.index') }}
                                @endif
                            " class="text-sm font-bold text-blue-600 hover:text-blue-700 flex items-center justify-center">
                                Voir tous mes engagements
                                <i class='bx bx-right-arrow-alt ml-1 text-lg'></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        
        </div>

        <!-- Right Column: Forms -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Personal Information Form -->
            <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="p-3 bg-gray-100 rounded-2xl text-gray-600">
                        <i class='bx bxs-id-card text-2xl'></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 font-sans">Informations Personnelles</h3>
                        <p class="text-sm text-gray-500">Mettez à jour votre nom et votre adresse email.</p>
                    </div>
                </div>

                <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf
                    @method('patch')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nom Complet</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Adresse Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-[#1e293b] text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:bg-gray-800 transition transform hover:-translate-y-0.5">
                            Sauvegarder les modifications
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security / Password Update Form -->
            <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="p-3 bg-red-50 rounded-2xl text-red-600">
                        <i class='bx bxs-lock-alt text-2xl'></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 font-sans">Sécurité du Compte</h3>
                        <p class="text-sm text-gray-500">Changez votre mot de passe pour maintenir la sécurité.</p>
                    </div>
                </div>

                <form method="post" action="{{ route('user.password.update') }}" class="space-y-6">
                    @csrf
                    @method('put')

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Mot de passe actuel</label>
                        <input type="password" name="current_password" required class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition">
                        @error('current_password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nouveau mot de passe</label>
                            <input type="password" name="password" required class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition">
                            @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Confirmer le mot de passe</label>
                            <input type="password" name="password_confirmation" required class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition transform hover:-translate-y-0.5">
                            Mettre à jour le mot de passe
                        </button>
                    </div>
                </form>
                
                <!-- Danger Zone Placeholder -->
            
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
function previewAndSubmit(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profile-preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
        
        // Auto-submit the form after a tiny delay to show the preview
        setTimeout(() => {
            input.form.submit();
        }, 300);
    }
}
</script>
@endpush
@endsection
