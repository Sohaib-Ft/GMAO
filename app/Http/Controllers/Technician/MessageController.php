<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display the list of conversations.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $search = $request->get('search');
        
        // Get all users the current user has exchanged messages with
        $conversations = User::where(function($query) use ($userId) {
                $query->whereHas('messagesSent', function($q) use ($userId) {
                    $q->where('receiver_id', $userId);
                })
                ->orWhereHas('messagesReceived', function($q) use ($userId) {
                    $q->where('sender_id', $userId);
                });
            })
            ->where('id', '!=', $userId)
            ->when($search, function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            })
            ->with(['messagesSent' => function($q) use ($userId) {
                $q->where('receiver_id', $userId)->latest();
            }, 'messagesReceived' => function($q) use ($userId) {
                $q->where('sender_id', $userId)->latest();
            }])
            ->get()
            ->map(function($user) {
                // Get the last message
                $lastSent = $user->messagesReceived->first();
                $lastReceived = $user->messagesSent->first();
                
                $user->last_message = collect([$lastSent, $lastReceived])
                    ->filter()
                    ->sortByDesc('created_at')
                    ->first();
                    
                return $user;
            })
            ->sortByDesc(function($user) {
                return $user->last_message ? $user->last_message->created_at : 0;
            });

        if ($request->ajax()) {
            return view('technician.messages._conversation_list', compact('conversations'))->render();
        }

        return view('technician.messages.index', compact('conversations'));
    }

    /**
     * Display a specific conversation.
     */
    public function chat(User $user)
    {
        if ($user->role !== 'technicien' || $user->id === Auth::id()) {
            abort(404);
        }

        $messages = Message::withTrashed() // Include soft deleted messages
            ->where(function($q) use ($user) {
                $q->where('sender_id', Auth::id())->where('receiver_id', $user->id);
            })
            ->orWhere(function($q) use ($user) {
                $q->where('sender_id', $user->id)->where('receiver_id', Auth::id());
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('technician.messages.chat', compact('user', 'messages'));
    }

    /**
     * Send a message.
     */
    public function send(Request $request, User $user)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id,
            'content' => $request->content
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('technician.messages.chat', $user);
    }

    /**
     * Delete a message.
     */
    public function destroy(Message $message)
    {
        // Only allow sender to delete their own message
        if ($message->sender_id !== Auth::id()) {
            abort(403, "Vous ne pouvez pas supprimer ce message.");
        }

        $message->delete();

        return back()->with('status', 'Message supprimé.');
    }
}
