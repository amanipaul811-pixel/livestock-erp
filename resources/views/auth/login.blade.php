@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="max-w-sm mx-auto mt-16 bg-white p-8 rounded shadow">
    <h1 class="text-xl font-semibold mb-6">Livestock ERP</h1>
    <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Password</label>
            <input type="password" name="password" required
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <button type="submit" class="w-full bg-gray-900 text-white rounded py-2 text-sm font-medium hover:bg-gray-700">
            Log in
        </button>
    </form>
</div>
@endsection
