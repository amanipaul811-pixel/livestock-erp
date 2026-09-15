@extends('layouts.app')

@section('title', 'New User')

@section('content')
<h1 class="text-2xl font-semibold mb-6">New User</h1>

<form method="POST" action="{{ route('users.store') }}" class="bg-white rounded shadow p-6 max-w-md space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium mb-1">Full Name</label>
        <input type="text" name="full_name" value="{{ old('full_name') }}" required class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Phone</label>
        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Password</label>
        <input type="password" name="password" required minlength="8" class="w-full border rounded px-3 py-2 text-sm">
        <p class="text-xs text-gray-500 mt-1">At least 8 characters, with upper and lower case letters and a number.</p>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Role</label>
        <select name="role_id" required class="w-full border rounded px-3 py-2 text-sm">
            <option value="">Select role</option>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>{{ $role->name }}</option>
            @endforeach
        </select>
    </div>
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))>
        Active
    </label>
    <div class="flex gap-2">
        <button type="submit" class="bg-gray-900 text-white text-sm px-4 py-2 rounded hover:bg-gray-700">Create User</button>
        <a href="{{ route('users.index') }}" class="border text-sm px-4 py-2 rounded hover:bg-gray-100">Cancel</a>
    </div>
</form>
@endsection
