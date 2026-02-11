<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the users (read-only for technician).
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Recherche par nom, email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtrer par rôle
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filtrer par statut (map 'actif'/'inactif' to boolean is_active)
        if ($request->filled('statut')) {
            if ($request->statut === 'actif') {
                $query->where('is_active', true);
            } elseif ($request->statut === 'inactif') {
                $query->where('is_active', false);
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('technician.users.index', compact('users'));
    }

    /**
     * Display the specified user (read-only).
     */
    public function show(User $user)
    {
        return view('technician.users.show', compact('user'));
    }
}
