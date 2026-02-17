<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMMS - @yield('title')</title>

    @vite('resources/css/app.css')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body
    class="bg-gray-100 text-gray-900 min-h-screen flex font-sans"
    x-data="{ sidebarOpen: true }"
    x-init="
        sidebarOpen = JSON.parse(localStorage.getItem('sidebarOpen') || 'true');
        $watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', JSON.stringify(val)))
    "
>

<!-- SIDEBAR -->
<aside
    class="bg-[#1e293b] text-gray-300 flex flex-col min-h-screen shadow-2xl transition-all duration-300 relative [&_*]:focus:outline-none"
    :class="sidebarOpen ? 'w-72' : 'w-20'"
>
    <!-- Toggle -->
    <button
        @click="sidebarOpen = !sidebarOpen"
        class="absolute -right-3 top-10 bg-blue-600 text-white w-7 h-7 rounded-full flex items-center justify-center hover:bg-blue-700 z-50 shadow-lg"
    >
        <i class="bx" :class="sidebarOpen ? 'bx-chevron-left' : 'bx-chevron-right'"></i>
    </button>

    <!-- Logo -->
    <div class="p-6 border-b border-gray-700 bg-[#151c2c]">
        <div class="flex flex-col items-center space-y-2">
            <img src="{{ asset('images/mibtech-logo.png') }}" class="h-12">
            <div x-show="sidebarOpen" class="text-center">
                <h1 class="text-[18px] text-white font-bold">MibTech CMMS</h1>
                <p class="text-[10px] text-blue-400 uppercase tracking-widest">Gestion de maintenance</p>
            </div>
        </div>
    </div>

    <!-- NAV -->
    <nav class="flex-1 px-4 py-6 space-y-1">

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
           class="flex items-center px-4 py-3 rounded-xl transition
           {{ request()->routeIs('dashboard*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
            <i class="bx bxs-dashboard text-xl mr-3"></i>
            <span x-show="sidebarOpen">Dashboard</span>
        </a>

      
  <div x-show="sidebarOpen"
     class="pt-4 text-[10px] text-gray-400 uppercase tracking-widest">
    Mon Espace
</div>

        <!-- Utilisateurs - Tous les rôles -->
        <a href="{{ 
            Auth::user()->role === 'admin' ? route('users.index') : 
            (Auth::user()->role === 'technicien' ? route('technician.users.index') : route('employee.users.index'))
        }}"
           class="flex items-center px-4 py-3 rounded-xl transition
           {{ request()->routeIs('users*') || request()->routeIs('technician.users*') || request()->routeIs('employee.users*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
            <i class="bx bxs-user text-xl mr-3"></i>
            <span x-show="sidebarOpen">Utilisateurs</span>
        </a>

        <!-- Équipements - Tous les rôles -->
        <a href="{{ 
            Auth::user()->role === 'admin' ? route('equipments.index') : 
            (Auth::user()->role === 'technicien' ? route('technician.equipments.index') : route('employee.equipments.index'))
        }}"
           class="flex items-center px-4 py-3 rounded-xl transition
           {{ request()->routeIs('equipments*') || request()->routeIs('technician.equipments*') || request()->routeIs('employee.equipments*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
            <i class="bx bxs-wrench text-xl mr-3"></i>
            <span x-show="sidebarOpen">Équipements</span>
        </a>

        <!-- Localisations - Tous les rôles -->
        <a href="{{ 
            Auth::user()->role === 'admin' ? route('localisations.index') : 
            (Auth::user()->role === 'technicien' ? route('technician.localisations.index') : route('employee.localisations.index'))
        }}"
           class="flex items-center px-4 py-3 rounded-xl transition
           {{ request()->routeIs('localisations*') || request()->routeIs('technician.localisations*') || request()->routeIs('employee.localisations*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
            <i class="bx bx-map-pin text-xl mr-3"></i>
            <span x-show="sidebarOpen">Localisations</span>
        </a>

        <!-- Types des Équipements - Admin & Technicien -->
        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'technicien')
            <a href="{{ Auth::user()->role === 'admin' ? route('equipment_types.index') : route('technician.equipment_types.index') }}"
               class="flex items-center px-4 py-3 rounded-xl transition
               {{ request()->routeIs('equipment_types*') || request()->routeIs('technician.equipment_types*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                <i class="bx bx-category text-xl mr-3"></i>
                <span x-show="sidebarOpen">Types des Équipements</span>
            </a>
        @endif

        <!-- Plans de Maintenance - Admin & Technicien -->
        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'technicien')
            <a href="{{ Auth::user()->role === 'admin' ? route('maintenance-plans.index') : route('technician.maintenance-plans.index') }}"
               class="flex items-center px-4 py-3 rounded-xl transition
               {{ request()->routeIs('maintenance-plans*') || request()->routeIs('technician.maintenance-plans*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                <i class="bx bx-calendar-check text-xl mr-3"></i>
                <span x-show="sidebarOpen">Maintenance préventive</span>
            </a>
        @endif
       

        <!-- Work Orders - Tous les rôles -->
    
        <a href="{{ 
            Auth::user()->role === 'admin' ? route('admin.workorders.index') : 
            (Auth::user()->role === 'technicien' ? route('technician.workorders.index') : route('employee.workorders.index'))
        }}"
           class="flex items-center px-4 py-3 rounded-xl transition
           {{ (request()->routeIs('admin.workorders*') || request()->routeIs('technician.workorders.index') || request()->routeIs('technician.workorders.create') || request()->routeIs('technician.workorders.edit') || request()->routeIs('technician.workorders.show') || request()->routeIs('employee.workorders*')) && !request()->routeIs('*.workorders.available') ? 'bg-blue-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
            <i class="bx bx-clipboard text-xl mr-3"></i>
            <span x-show="sidebarOpen">Work Orders</span>
        </a>

        <div x-show="sidebarOpen" class="pt-4 text-[10px] text-gray-400 uppercase tracking-widest">
            Compte
        </div>

        <a href="{{ route('profile.edit') }}"
           class="flex items-center px-4 py-3 rounded-xl transition
           {{ request()->routeIs('profile*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
            <i class="bx bxs-user-circle text-xl mr-3"></i>
            <span x-show="sidebarOpen">Mon Profil</span>
        </a>
    </nav>

    <!-- LOGOUT -->
    <div class="p-4 border-t border-gray-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="color: #d9dde2 !important;" class="w-full flex items-center px-4 py-3 rounded-xl hover:text-red-500 hover:bg-red-500/10 transition focus:outline-none" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#d1d5db'">
                <i class="bx bx-log-out text-xl mr-3" style="color: inherit;"></i>
                <span x-show="sidebarOpen" style="color: inherit;">Déconnexion</span>
            </button>
        </form>
    </div>
</aside>

<!-- MAIN -->
<main class="flex-1 p-8">
    <header class="mb-6 border-b pb-4">
        <h2 class="text-3xl font-bold">@yield('header')</h2>
    </header>

    @yield('content')
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const flashes = document.querySelectorAll('[role="alert"], [data-flash]');
    flashes.forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity 300ms ease, transform 300ms ease';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-6px)';
            setTimeout(() => { try { el.remove(); } catch(e){} }, 350);
        }, 5000);
    });
});
</script>
@stack('scripts')
</body>
</html>
