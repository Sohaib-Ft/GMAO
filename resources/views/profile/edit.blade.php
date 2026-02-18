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
