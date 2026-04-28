@props(['note'])

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
    <div class="p-5">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-gray-900 mb-1 truncate">{{ $note->title }}</h3>
                @if($note->description)
                    <p class="text-sm text-gray-500 line-clamp-2">{{ Str::limit($note->description, 100) }}</p>
                @endif
            </div>
        </div>
        <div class="mt-3 flex items-center justify-between text-xs text-gray-400">
            <span>{{ $note->user?->full_name }}</span>
            <span>{{ $note->created_at->format('Y/m/d') }}</span>
        </div>
        <a href="{{ route('notes.show', $note) }}" class="block mt-3 text-center bg-orange-50 text-orange-600 py-2 rounded-lg text-sm font-medium hover:bg-orange-100 transition-colors">عرض الملخص</a>
    </div>
</div>
