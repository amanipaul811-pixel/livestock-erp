<button type="button"
        onclick="if (window.history.length > 1 && document.referrer && document.referrer.startsWith(window.location.origin)) { history.back(); } else { window.location = {{ Js::from($fallback ?? route('dashboard')) }}; }"
        class="shrink-0 rounded-md p-1.5 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800"
        title="Go back" aria-label="Go back">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 19l-7-7 7-7"/></svg>
</button>
