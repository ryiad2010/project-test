{{-- resources/views/filament/custom/footer.blade.php --}}

<footer class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400 border-t border-gray-950/5 dark:border-white/5">
    <p>
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </p>
    <p class="mt-1">
        <a href="/dashboard" class="underline hover:text-gray-700 dark:hover:text-gray-200">Terms of Service</a>
        <span class="px-2">|</span>
        <a href="/dashboard" class="underline hover:text-gray-700 dark:hover:text-gray-200">Privacy Policy</a>
    </p>

</footer>
