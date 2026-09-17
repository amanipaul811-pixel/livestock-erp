<a href="{{ route('scan') }}"
   class="rounded-md p-2 {{ request()->routeIs('scan') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400' }} hover:bg-gray-100 dark:hover:bg-gray-800"
   title="Scan a tag" aria-label="Scan a tag">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8V4h4M4 16v4h4M16 4h4v4M16 20h4v-4M4 12h16"/></svg>
</a>
