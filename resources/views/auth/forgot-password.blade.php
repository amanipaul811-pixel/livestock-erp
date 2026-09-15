@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="max-w-sm mx-auto mt-16 bg-white p-8 rounded shadow">
    <h1 class="text-xl font-semibold mb-2">Forgot Password</h1>
    <p class="text-sm text-gray-500 mb-6">Enter your email and we'll send you a reset link.</p>

    @if (session('status'))
        <div class="mb-4 rounded bg-green-100 text-green-800 px-4 py-2 text-sm">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <button type="submit" class="w-full bg-gray-900 text-white rounded py-2 text-sm font-medium hover:bg-gray-700">
            Send Reset Link
        </button>
    </form>

    <a href="{{ route('login') }}" class="block text-center text-sm text-gray-500 hover:underline mt-4">Back to login</a>
</div>
@endsection
