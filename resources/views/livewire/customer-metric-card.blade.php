<div class="p-4 bg-white border rounded-lg shadow-sm flex items-center gap-4 dark:bg-gray-900 dark:border-white/10">
    <div class="text-primary-500">
        @svg($icon, 'w-8 h-8')
    </div>

    <div>
        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
            {{ str_replace('_', ' ', $metricType) }}
        </p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ $metricType === 'total_spent' ? '$' . number_format($value, 2) : $value }}
        </p>
    </div>
</div>
