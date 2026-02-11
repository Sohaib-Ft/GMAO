<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Fetch user's work orders based on their role
        $workOrders = collect();
        
        if ($user->role === 'employe') {
            // For employees, show work orders they created
            $workOrders = $user->employeWorkOrders()
                ->with(['equipement', 'technicien'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        } elseif ($user->role === 'technicien' || $user->role === 'technician') {
            // For technicians, show work orders assigned to them
            $workOrders = $user->technicienWorkOrders()
                ->with(['equipement', 'employe'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        } elseif ($user->role === 'admin') {
            // For admins, show recent work orders (all)
            $workOrders = \App\Models\WorkOrder::with(['equipement', 'employe', 'technicien'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }
        
        return view('profile.edit', [
            'user' => $user,
            'workOrders' => $workOrders,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        // Verify current password (use native password_verify to accept different hash algorithms)
        if (! password_verify($request->input('current_password'), $user->password)) {
            return Redirect::back()->withErrors(['current_password' => 'The provided password does not match our records.']);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'password-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
