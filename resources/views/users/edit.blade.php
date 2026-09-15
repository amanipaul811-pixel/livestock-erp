@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<h1 class="text-2xl font-semibold mb-6">Edit {{ $user->full_name }}</h1>

<form method="POST" action="{{ route('users.update', $user) }}" class="bg-white rounded shadow p-6 max-w-md space-y-4">
    @csrf
    @method('PUT')
    <div>
        <label class="block text-sm font-medium mb-1">Full Name</label>
        <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">New Password</label>
        <input type="password" name="password" minlength="8" placeholder="Leave blank to keep current password" class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Role</label>
        <select name="role_id" required class="w-full border rounded px-3 py-2 text-sm">
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id) == $role->id)>{{ $role->name }}</option>
            @endforeach
        </select>
    </div>
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active))>
        Active
    </label>
    <div class="flex gap-2">
        <button type="submit" class="bg-gray-900 text-white text-sm px-4 py-2 rounded hover:bg-gray-700">Save</button>
        <a href="{{ route('users.index') }}" class="border text-sm px-4 py-2 rounded hover:bg-gray-100">Cancel</a>
    </div>
</form>
@endsection
