@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="max-w-sm mx-auto mt-16 bg-white dark:bg-gray-900 p-8 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800">
    <h1 class="text-xl font-semibold mb-6">Livestock ERP</h1>
    <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Password</label>
            <input type="password" name="password" required
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
        <button type="submit" class="w-full bg-indigo-600 text-white rounded-md py-2 text-sm font-medium hover:bg-indigo-700">
            Log in
        </button>
    </form>
    <a href="{{ route('password.request') }}" class="block text-center text-sm text-gray-500 dark:text-gray-400 hover:underline mt-4">Forgot password?</a>
</div>
@endsection
