@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="flex items-center gap-3 mb-6">
    @include('partials.back-button')
    <h1 class="text-2xl font-semibold">{{ $title }}</h1>
</div>

<p class="text-sm text-gray-500 dark:text-gray-400 mb-4 max-w-xl">{{ $blurb }}</p>

<div class="grid gap-3 sm:grid-cols-2 max-w-3xl">
    @foreach ($links as [$label, $routeName, $description])
        <a href="{{ route($routeName) }}"
           class="block rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-indigo-500 dark:border-gray-800 dark:bg-gray-900 dark:hover:border-indigo-400">
            <p class="font-medium text-indigo-700 dark:text-indigo-300">{{ $label }}</p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
        </a>
    @endforeach
</div>
@endsection
