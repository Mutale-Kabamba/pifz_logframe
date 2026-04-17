<div class="space-y-4">
    @forelse($logs as $log)
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-primary-600 dark:text-primary-400">
                    {{ $log->actual_value }}
                </span>
                <span class="text-xs text-gray-500">
                    {{ $log->recorded_at->format('M d, Y H:i') }}
                </span>
            </div>

            @if($log->evidence_link)
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                    <span class="font-medium">Evidence:</span>
                    <a href="{{ $log->evidence_link }}" target="_blank" rel="noopener noreferrer" class="text-primary-500 hover:underline">
                        {{ $log->evidence_link }}
                    </a>
                </p>
            @endif

            @if($log->notes)
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                    <span class="font-medium">Notes:</span> {{ $log->notes }}
                </p>
            @endif

            <p class="text-xs text-gray-400 mt-2">
                Recorded by: {{ $log->recorder?->name ?? 'Unknown' }}
            </p>
        </div>
    @empty
        <div class="text-center py-8 text-gray-500">
            <p>No tracking logs recorded yet for this item.</p>
        </div>
    @endforelse
</div>
