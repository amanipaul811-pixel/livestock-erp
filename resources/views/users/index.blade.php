@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Users</h1>
    <a href="{{ route('users.create') }}" class="bg-gray-900 text-white text-sm px-4 py-2 rounded hover:bg-gray-700">+ New User</a>
</div>

<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-100 text-left text-gray-600">
            <tr><th class="px-4 py-2">Name</th><th class="px-4 py-2">Email</th><th class="px-4 py-2">Role</th><th class="px-4 py-2">Status</th><th class="px-4 py-2"></th></tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr class="border-t">
                    <td class="px-4 py-2 font-medium">{{ $user->full_name }}</td>
                    <td class="px-4 py-2">{{ $user->email }}</td>
                    <td class="px-4 py-2">{{ $user->role->name }}</td>
                    <td class="px-4 py-2">
                        <span @class([
                            'px-2 py-0.5 rounded text-xs font-medium',
                            'bg-green-100 text-green-700' => $user->is_active,
                            'bg-gray-200 text-gray-700' => ! $user->is_active,
                        ])>{{ $user->is_active ? 'active' : 'inactive' }}</span>
                    </td>
                    <td class="px-4 py-2 text-right">
                        <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:underline text-xs">Edit</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">No users yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
