<?php

namespace App\Http\Controllers\Admin;

use App\Models\Reclamation;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\TemporaryPasswordMail;

class ReclamationController extends Controller
{
    /**
     * Formulaire public de réclamation
     */
    public function create()
    {
        return view('reclamations.create');
    }

    /**
     * Enregistre une réclamation
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'nullable|string|max:1000',
        ]);

        // Vérifier qu'un utilisateur existe avec ce nom ET cet email
        $user = User::where('email', $request->email)
                    ->where('name', $request->nom)
                    ->first();

        if (!$user) {
            return back()->withInput()->with('error', 'Aucun utilisateur trouvé avec ce nom et cet email. La demande n\'a pas été envoyée.');
        }

        Reclamation::create([
            'nom' => $request->nom,
            'email' => $request->email,
            'message' => $request->message,
            'user_id' => $user->id,
            'status' => 'en_attente',
        ]);

        return redirect()->route('login')->with('status', 'Votre demande a été envoyée avec succès. L\'administrateur traitera votre demande prochainement.');
    }

    /**
     * Liste des réclamations pour l'admin
     */
    public function index(Request $request)
    {
        $query = Reclamation::with('user');

        // Recherche par nom ou email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Filtre par rôle (via la relation user)
        if ($request->filled('role')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        // Filtre par date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reclamations = $query->latest()->paginate(10);

        $statuses = [
            'en_attente' => 'En attente',
            'traitee' => 'Traitée',
            'refusee' => 'Refusée',
        ];

        return view('admin.reclamations.index', compact('reclamations', 'statuses'));
    }

    /**
     * Traite une réclamation : génère mdp, envoie mail, marque comme traitée
     */
    public function process(Request $request, Reclamation $reclamation)
    {
        // On permet de refaire le traitement même si déjà traitée (pour renvoyer le mail si besoin)
        
        // Trouver l'utilisateur lié
        $user = $reclamation->user ?: User::where('email', $reclamation->email)->first();

        if (!$user) {
            $reclamation->update([
                'status' => 'refusee',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);
            return back()->with('error', 'Aucun utilisateur trouvé avec cet email. La demande a été refusée.');
        }

        // Générer un mot de passe aléatoire (12 caractères comme dans MailerController)
        $newPassword = Str::random(12);
        
        // Mettre à jour l'utilisateur
        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        // Envoyer le mail
        try {
            Mail::to($user->email)->send(new TemporaryPasswordMail($user, $newPassword));
            
            // Marquer la réclamation comme traitée
            $reclamation->update([
                'status' => 'traitee',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            return back()->with('status', 'Un nouveau mot de passe a été généré et envoyé à ' . $user->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Le mot de passe a été changé mais l\'envoi du mail a échoué : ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified reclamation.
     */
    public function destroy(Reclamation $reclamation)
    {
        $reclamation->delete();

        return redirect()->route('reclamations.index')->with('status', 'Réclamation supprimée avec succès.');
    }
}
