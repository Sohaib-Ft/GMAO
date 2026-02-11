<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

// Include additional auth routes if present (password reset, verification, etc.)
if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
}

// Page d'accueil
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Routes d'authentification (guest uniquement)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Réclamation de mot de passe perdu
    Route::get('/reclamation', [\App\Http\Controllers\Admin\ReclamationController::class, 'create'])->name('reclamation.create');
    Route::post('/reclamation', [\App\Http\Controllers\Admin\ReclamationController::class, 'store'])->name('reclamation.store');
});

// Routes protégées (auth uniquement)
Route::middleware(['auth'])->group(function () {

    // Resend email verification (used by verification views)
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');


    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Redirection vers le dashboard selon rôle
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->role === 'admin') {
            return redirect()->route('dashboard.admin');
        } elseif ($user->role === 'technicien') {
            return redirect()->route('dashboard.technicien');
        } else {
            return redirect()->route('dashboard.employe');
        }
    })->name('dashboard');

    // Dashboards sécurisés selon rôle
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/dashboard/admin', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard.admin');

        // Admin: Users list
        Route::get('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');

        // Admin: Import equipments (CSV)
        Route::get('/admin/equipments/import', [\App\Http\Controllers\Admin\EquipmentController::class, 'showImport'])->name('equipments.import');
        Route::post('/admin/equipments/import', [\App\Http\Controllers\Admin\EquipmentController::class, 'import'])->name('equipments.import.store');

        // Admin: Equipments Management
        Route::resource('/admin/equipments', \App\Http\Controllers\Admin\EquipmentController::class);

        // Admin: Reclamation Management
        Route::get('/admin/reclamations', [\App\Http\Controllers\Admin\ReclamationController::class, 'index'])->name('reclamations.index');
        Route::post('/admin/reclamations/{reclamation}/process', [\App\Http\Controllers\Admin\ReclamationController::class, 'process'])->name('reclamations.process');
        Route::delete('/admin/reclamations/{reclamation}', [\App\Http\Controllers\Admin\ReclamationController::class, 'destroy'])->name('reclamations.destroy');

        // Admin: Create user form + store
        Route::get('/admin/users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
        Route::post('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
        Route::get('/admin/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
        Route::get('/admin/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('/admin/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::patch('/admin/users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::delete('/admin/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
        // Admin: WorkOrders
        Route::prefix('/admin/workorders')->name('admin.workorders.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\WorkOrderController::class, 'index'])->name('index');
            Route::get('/{workOrder}', [\App\Http\Controllers\Admin\WorkOrderController::class, 'show'])->name('show');
            Route::get('/{workOrder}/edit', [\App\Http\Controllers\Admin\WorkOrderController::class, 'edit'])->name('edit');
        });

        // Admin: Settings (Localisations & Equipment Types)
        Route::resource('/admin/localisations', \App\Http\Controllers\Admin\LocalisationController::class)->except(['show', 'create', 'edit']);
        Route::resource('/admin/equipment_types', \App\Http\Controllers\Admin\EquipmentTypeController::class)->except(['show', 'create', 'edit']);
    });

    Route::middleware(['role:technicien,technician'])->group(function () {
        Route::get('/dashboard/technicien', [\App\Http\Controllers\Technician\DashboardController::class, 'index'])->name('dashboard.technicien');

        // Technicien: Vue lecture seule des utilisateurs
        Route::get('/technicien/users', [\App\Http\Controllers\Technician\UserController::class, 'index'])->name('technician.users.index');
        Route::get('/technicien/users/{user}', [\App\Http\Controllers\Technician\UserController::class, 'show'])->name('technician.users.show');

        // Technicien: Vue lecture seule des équipements
        Route::get('/technicien/equipments', [\App\Http\Controllers\Technician\EquipmentController::class, 'index'])->name('technician.equipments.index');
        Route::get('/technicien/equipments/{equipment}', [\App\Http\Controllers\Technician\EquipmentController::class, 'show'])->name('technician.equipments.show');

        // Technicien: Gestion des interventions (Workflow )
        // Routes de listing (sans paramètres dynamiques)
        Route::get('/technicien/workorders', [\App\Http\Controllers\Technician\WorkOrderController::class, 'index'])->name('technician.workorders.index');
        Route::get('/technicien/workorders/available', [\App\Http\Controllers\Technician\WorkOrderController::class, 'available'])->name('technician.workorders.available');
        Route::get('/technicien/workorders/history', [\App\Http\Controllers\Technician\WorkOrderController::class, 'history'])->name('technician.workorders.history');
        
        // Actions POST (doivent être avant la route {workOrder} générique)
        Route::post('/technicien/workorders/{workOrder}/assign', [\App\Http\Controllers\Technician\WorkOrderController::class, 'assign'])->name('technician.workorders.assign');
        Route::post('/technicien/workorders/{workOrder}/start', [\App\Http\Controllers\Technician\WorkOrderController::class, 'start'])->name('technician.workorders.start');
        Route::post('/technicien/workorders/{workOrder}/complete', [\App\Http\Controllers\Technician\WorkOrderController::class, 'complete'])->name('technician.workorders.complete');
        Route::post('/technicien/workorders/{workOrder}/irreparable', [\App\Http\Controllers\Technician\WorkOrderController::class, 'markAsIrrepairable'])->name('technician.workorders.irreparable');
        
        // Fallback GET routes pour éviter les erreurs 405
        Route::get('/technicien/workorders/{workOrder}/assign', function() {
            return redirect()->route('technician.workorders.available')->with('error', 'Veuillez utiliser le bouton d\'assignation.');
        });
        Route::get('/technicien/workorders/{workOrder}/start', function() {
            return redirect()->route('technician.workorders.index')->with('error', 'Veuillez utiliser le bouton de démarrage.');
        });
        Route::get('/technicien/workorders/{workOrder}/complete', function() {
            return redirect()->route('technician.workorders.index')->with('error', 'Veuillez utiliser le bouton de complétion.');
        });
        Route::get('/technicien/workorders/{workOrder}/irreparable', function() {
            return redirect()->route('technician.workorders.index')->with('error', 'Veuillez utiliser le formulaire approprié.');
        });
        
        // Route show générique (doit être en dernier)
        Route::get('/technicien/workorders/{workOrder}', [\App\Http\Controllers\Technician\WorkOrderController::class, 'show'])->name('technician.workorders.show');

        // Messagerie
        Route::get('/technicien/messages', [\App\Http\Controllers\Technician\MessageController::class, 'index'])->name('technician.messages.index');
        Route::get('/technicien/messages/{user}', [\App\Http\Controllers\Technician\MessageController::class, 'chat'])->name('technician.messages.chat');
        Route::post('/technicien/messages/{user}', [\App\Http\Controllers\Technician\MessageController::class, 'send'])->name('technician.messages.send');
        Route::delete('/technicien/messages/{message}', [\App\Http\Controllers\Technician\MessageController::class, 'destroy'])->name('technician.messages.destroy');

        // Technicien: Localisations (lecture seule)
        Route::get('/technicien/localisations', [\App\Http\Controllers\Technician\LocalisationController::class, 'index'])->name('technician.localisations.index');
        Route::get('/technicien/localisations/{localisation}', [\App\Http\Controllers\Technician\LocalisationController::class, 'show'])->name('technician.localisations.show');

        // Technicien: Types d'équipements (lecture seule)
        Route::get('/technicien/equipment_types', [\App\Http\Controllers\Technician\EquipmentTypeController::class, 'index'])->name('technician.equipment_types.index');
    });

    Route::middleware(['role:employe'])->group(function () {
        Route::get('/dashboard/employe', [\App\Http\Controllers\Employee\DashboardController::class, 'index'])->name('dashboard.employe');

        // Employe: Vue lecture seule des utilisateurs
        Route::get('/employee/users', [\App\Http\Controllers\Employee\UserController::class, 'index'])->name('employee.users.index');
        Route::get('/employee/users/{user}', [\App\Http\Controllers\Employee\UserController::class, 'show'])->name('employee.users.show');

        // Employe: Equipments View
        Route::get('/equipments', [\App\Http\Controllers\Employee\EquipmentController::class, 'index'])->name('employee.equipments.index');
        Route::get('/equipments/{equipment}', [\App\Http\Controllers\Employee\EquipmentController::class, 'show'])->name('employee.equipments.show');
        
        // Employe: WorkOrders index
        Route::get('/workorders', [\App\Http\Controllers\Employee\WorkOrderController::class, 'index'])->name('employee.workorders.index');

        // Signalement / "Assignation" de panne
        Route::get('/workorders/create', [\App\Http\Controllers\Employee\WorkOrderController::class, 'create'])->name('employee.workorders.create');
        Route::post('/workorders', [\App\Http\Controllers\Employee\WorkOrderController::class, 'store'])->name('employee.workorders.store');
        Route::delete('/workorders/{workOrder}', [\App\Http\Controllers\Employee\WorkOrderController::class, 'destroy'])->name('employee.workorders.destroy');

        // Employe: Localisations (lecture seule)
        Route::get('/localisations', [\App\Http\Controllers\Employee\LocalisationController::class, 'index'])->name('employee.localisations.index');
        Route::get('/localisations/{localisation}', [\App\Http\Controllers\Employee\LocalisationController::class, 'show'])->name('employee.localisations.show');
    });
    

    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/user/password', [ProfileController::class, 'updatePassword'])->name('user.password.update');
    
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});