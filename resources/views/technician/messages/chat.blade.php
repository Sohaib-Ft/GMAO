@extends('layouts.app')

@section('title', 'Chat avec ' . $user->name)
@section('header', 'Discussion')

@section('content')
<div class="max-w-4xl mx-auto h-[calc(100vh-200px)] flex flex-col">
    <!-- Chat Header -->
    <div class="bg-white p-6 rounded-t-3xl border-x border-t border-gray-100 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-4">
            <a href="{{ route('technician.messages.index') }}" class="h-10 w-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition">
                <i class='bx bx-chevron-left text-2xl'></i>
            </a>
            <div class="h-12 w-12 bg-gray-900 text-white rounded-xl flex items-center justify-center text-xl font-bold shadow-md">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <h3 class="font-bold text-gray-900">{{ $user->name }}</h3>

            </div>
        </div>
        <a href="{{ route('technician.team.show', $user) }}" class="text-gray-400 hover:text-blue-600 transition">
            <i class='bx bx-info-circle text-2xl'></i>
        </a>
    </div>

    <!-- Messages Container -->
    <div class="flex-1 bg-white border-x border-gray-100 overflow-y-auto p-6 space-y-4" id="messages-box">
        @forelse($messages as $msg)
            <div class="flex {{ $msg->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }} group">
                <div class="max-w-[80%] relative">
                    <div class="p-4 rounded-2xl text-sm {{ 
                        $msg->trashed() 
                        ? 'bg-gray-100 text-gray-400 italic border border-gray-200 shadow-none' // Deleted style
                        : ($msg->sender_id === Auth::id() ? 'bg-blue-600 text-white rounded-br-none shadow-lg shadow-blue-100' : 'bg-gray-100 text-gray-800 rounded-bl-none') 
                    }}">
                        @if($msg->trashed())
                            <div class="flex items-center gap-2">
                                <i class='bx bx-block text-base'></i>
                                Ce message a été supprimé
                            </div>
                        @else
                            {{ $msg->content }}
                        @endif
                    </div>
                    
                    @if(!$msg->trashed())
                        <div class="flex items-center gap-2 mt-1 {{ $msg->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                            <p class="text-[10px] text-gray-400">
                                {{ $msg->created_at->format('H:i') }}
                            </p>
                            @if($msg->sender_id === Auth::id())
                                <button type="button" class="text-red-400 hover:text-red-600 p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200" onclick="openDeleteModal('{{ route('technician.messages.destroy', $msg->id) }}')">
                                    <i class='bx bx-trash text-xs'></i>
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="h-full flex flex-col items-center justify-center text-center p-10">
                <div class="h-16 w-16 bg-blue-50 text-blue-300 rounded-3xl flex items-center justify-center mb-4">
                    <i class='bx bx-conversation text-3xl'></i>
                </div>
                <p class="text-gray-400 italic text-sm">Fin du mystère. Envoyez le premier message à {{ $user->name }}.</p>
            </div>
        @endforelse
    </div>

    <!-- Message Input -->
    <div class="bg-gray-50 p-6 rounded-b-3xl border-x border-b border-gray-100 shadow-xl shadow-gray-200/20">
        <form action="{{ route('technician.messages.send', $user) }}" method="POST" class="flex gap-4">
            @csrf
            <div class="flex-1 relative">
                <input type="text" name="content" required autocomplete="off"
                    placeholder="Tapez votre message ici..." 
                    class="w-full px-6 py-4 bg-white border-none rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 transition-all shadow-sm">
            </div>
            <button type="submit" class="h-14 w-14 bg-gray-900 text-white rounded-2xl flex items-center justify-center hover:bg-black transition shadow-xl shadow-gray-200">
                <i class='bx bxs-send text-2xl'></i>
            </button>
        </form>
    </div>
</div>

<script>
    function openDeleteModal(actionUrl) {
        document.getElementById('deleteForm').action = actionUrl;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const box = document.getElementById('messages-box');
        box.scrollTop = box.scrollHeight;
    });
</script>

<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="closeDeleteModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full border border-gray-100">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class='bx bx-trash text-red-600 text-xl'></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                            Supprimer le message
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Voulez-vous vraiment effacer ce message ? Cette action est irréversible.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm shadow-red-200 transition-all">
                        Supprimer
                    </button>
                </form>
                <button type="button" onclick="closeDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
