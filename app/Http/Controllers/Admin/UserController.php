<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeTempPassword;

class UserController extends Controller
{
    public function create()
    {
        return view('admin.users.create');
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        } else {
            $query->whereIn('role', ['admin', 'employe', 'technicien']);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        // Use the provided password (permanent) and do not mark first_login
        $password = $data['password'];

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => Hash::make($password),
            'first_login' => false,
        ]);

        // Déléguer l'envoi d'email de bienvenue au MailerController
        (new \App\Http\Controllers\MailerController())->sendWelcomeTempPassword($user, $password);

        return redirect()->route('users.index')->with('status', 'Utilisateur créé avec succès.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Display the specified user profile.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function update(\App\Http\Requests\UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('users.index')->with('status', 'Utilisateur mis à jour avec succès.');
    }

    public function toggleStatus(User $user)
    {
        // Prevent admin from deactivating themselves
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        if ($user->is_active) {
            $status = 'réactivé';
             try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\UserActivated($user));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Erreur d'envoi d'email : " . $e->getMessage());
            }
        } else {
             $status = 'désactivé';
             try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\UserDeactivated($user));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Erreur d'envoi d'email : " . $e->getMessage());
            }
        }

        return back()->with('status', "Le compte de {$user->name} a été {$status} avec succès.");
    }

   public function destroy(User $user)
    {
        // 1. Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Condition: "L’utilisateur n’a jamais créé de Work Orders"
        if ($user->employeWorkOrders()->exists()) {
            return back()->with('error', "Suppression impossible : Cet utilisateur a créé des Ordres de Travail. Veuillez le DÉSACTIVER pour préserver l'audit et l'historique.");
        }

        // Condition: "L’utilisateur n’est pas assigné à des Work Orders"
        if ($user->technicienWorkOrders()->exists()) {
            return back()->with('error', "Suppression impossible : Cet utilisateur est intervenu sur des Ordres de Travail. Veuillez le DÉSACTIVER pour préserver l'historique technique.");
        }

        // Condition: Interventions performed
        if ($user->interventions()->exists()) {
            return back()->with('error', "Suppression impossible : Des interventions sont liées à ce technicien. Veuillez le DÉSACTIVER.");
        }


        // Hard delete chats (as they are personal and don't impact technical audit)
        \App\Models\Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->delete();

        // 4. Soft Delete the user
        // Only reachable if "clean" account (error/duplicate)
        $user->delete();

        return redirect()->route('users.index')->with('status', "L'utilisateur {$user->name} a été supprimé définitivement (Compte sans historique).");
    }
}
