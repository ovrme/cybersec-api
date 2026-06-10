@extends('admin.layout')
@section('title', 'Users')
@section('heading', 'User management')

@section('content')
    <div class="bg-card border border-line rounded-2xl overflow-hidden card-shadow">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-line text-left text-slate text-xs uppercase tracking-wide">
                    <th class="px-5 py-3 font-medium">Name</th>
                    <th class="px-5 py-3 font-medium">Email</th>
                    <th class="px-5 py-3 font-medium">Role</th>
                    <th class="px-5 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-5 py-3 font-medium">{{ $user->name }}</td>
                        <td class="px-5 py-3 text-slate">{{ $user->email }}</td>
                        <td class="px-5 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $user->role === 'admin' ? 'bg-amber/10 text-amber' : 'bg-paper text-slate' }}">{{ $user->role }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Toggle role --}}
                                <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="role" value="{{ $user->role === 'admin' ? 'user' : 'admin' }}">
                                    <button class="text-xs border border-line px-2.5 py-1 rounded-lg hover:border-teal hover:text-teal transition">
                                        {{ $user->role === 'admin' ? 'Make user' : 'Make admin' }}
                                    </button>
                                </form>
                                {{-- Delete --}}
                                @if ($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.delete', $user->id) }}"
                                          onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs border border-coral/40 text-coral px-2.5 py-1 rounded-lg hover:bg-coral/10 transition">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
@endsection
