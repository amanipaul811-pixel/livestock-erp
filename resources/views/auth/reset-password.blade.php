@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="max-w-sm mx-auto mt-16 bg-white dark:bg-gray-900 p-8 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800">
    <h1 class="text-xl font-semibold mb-6">Reset Password</h1>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $email) }}" required
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">New Password</label>
            <input type="password" name="password" required minlength="8"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">At least 8 characters, with upper and lower case letters and a number.</p>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation" required minlength="8"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
        <button type="submit" class="w-full bg-indigo-600 text-white rounded-md py-2 text-sm font-medium hover:bg-indigo-700">
            Reset Password
        </button>
    </form>
</div>
@endsection
