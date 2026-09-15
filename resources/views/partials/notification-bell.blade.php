@php
    $unreadNotifications = auth()->user()->unreadNotifications;
    $recentNotifications = auth()->user()->notifications()->latest()->take(10)->get();
@endphp
<div x-data="{ open: false }" @click.outside="open = false" class="relative">
    <button @click="open = !open" class="relative rounded-md p-2 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800" title="Notifications">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        @if ($unreadNotifications->isNotEmpty())
            <span class="absolute top-1 right-1 h-2 w-2 rounded-full bg-red-500"></span>
        @endif
    </button>
    <div x-show="open" x-cloak
         class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-900 rounded-lg shadow-lg border border-gray-200 dark:border-gray-800 text-sm z-50">
        <div class="flex items-center justify-between px-3 py-2 border-b border-gray-200 dark:border-gray-800">
            <span class="font-semibold">Notifications</span>
            @if ($unreadNotifications->isNotEmpty())
                <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                    @csrf
                    <button class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Mark all read</button>
                </form>
            @endif
        </div>
        <div class="max-h-80 overflow-y-auto">
            @forelse ($recentNotifications as $notification)
                <a href="{{ route('notifications.open', $notification) }}"
                   class="block px-3 py-2 border-b border-gray-100 dark:border-gray-800 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800 {{ $notification->read_at ? 'text-gray-500 dark:text-gray-400' : 'font-medium' }}">
                    {{ $notification->data['message'] ?? '' }}
                    <div class="text-xs font-normal text-gray-400 dark:text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                </a>
            @empty
                <p class="px-3 py-4 text-center text-gray-500 dark:text-gray-400">No notifications yet.</p>
            @endforelse
        </div>
    </div>
</div>
