@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        @include('partials.back-button')
        <h1 class="text-2xl font-semibold">Users</h1>
    </div>
    <a href="{{ route('users.create') }}" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700">+ New User</a>
</div>

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
            <tr><th class="px-4 py-2">Name</th><th class="px-4 py-2">Email</th><th class="px-4 py-2">Role</th><th class="px-4 py-2">Status</th><th class="px-4 py-2"></th></tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr class="border-t border-gray-200 dark:border-gray-800">
                    <td class="px-4 py-2 font-medium">{{ $user->full_name }}</td>
                    <td class="px-4 py-2">{{ $user->email }}</td>
                    <td class="px-4 py-2">{{ $user->role->name }}</td>
                    <td class="px-4 py-2">
                        <span @class([
                            'px-2 py-0.5 rounded text-xs font-medium',
                            'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400' => $user->is_active,
                            'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' => ! $user->is_active,
                        ])>{{ $user->is_active ? 'active' : 'inactive' }}</span>
                    </td>
                    <td class="px-4 py-2 text-right">
                        <a href="{{ route('users.edit', $user) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs">Edit</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No users yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
