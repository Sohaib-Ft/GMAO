<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
       
<h3 class="text-center text-2xl font-extrabold text-gray-800 mb-4">
    Mot de passe oublié ?
</h3>
        <p class="text-gray-500">
            Aucun problème. Indiquez-nous simplement votre adresse email et nous vous enverrons un lien de réinitialisation qui vous permettra d'en choisir un nouveau.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="font-bold text-gray-700" class="em"  />
            <x-text-input id="email" 
                class="block mt-1 w-full border-gray-100 bg-gray-50 focus:bg-white transition-all rounded-xl py-3" 
                type="email" 
                name="email" 
                :value="old('email')" 
                required 
                autofocus 
                placeholder="Entrez votre adresse email" 
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between pt-4 ">
             <a href="{{ route('login') }}" class="text-sm text-blue-500 hover:text-blue-600 transition inline-flex items-center retour">
                <i class='bx bx-arrow-back mr-1 500'></i> Retour au login
            </a>
            <x-primary-button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl shadow-lg shadow-blue-100 transition-all font-bold bbtn">
                {{ __('Envoyer le lien') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

<style>
    
.bbtn {
    background-color: rgb(59, 130, 246);
    color: white;
    transition: background-color 0.2s;
}
.bbtn:hover {
    background-color: rgb(37, 99, 235);
}

.retour:hover {
    text-decoration: underline;
}

</style>