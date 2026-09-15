@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="max-w-sm mx-auto mt-16 bg-white p-8 rounded shadow">
    <h1 class="text-xl font-semibold mb-6">Reset Password</h1>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $email) }}" required
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">New Password</label>
            <input type="password" name="password" required minlength="8"
                   class="w-full border rounded px-3 py-2 text-sm">
            <p class="text-xs text-gray-500 mt-1">At least 8 characters, with upper and lower case letters and a number.</p>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation" required minlength="8"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <button type="submit" class="w-full bg-gray-900 text-white rounded py-2 text-sm font-medium hover:bg-gray-700">
            Reset Password
        </button>
    </form>
</div>
@endsection
