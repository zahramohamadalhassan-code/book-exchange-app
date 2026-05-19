@props(['note'])

@php
    $isOwner = auth()->check() && auth()->id() === $note->user_id;
    $favorite = null;
    $isFavorited = false;
    if (auth()->check() && !$isOwner) {
        $favorite = auth()->user()->favorites()
            ->where('favoritable_id', $note->id)
            ->where('favoritable_type', App\Models\DigitalNote::class)
            ->first();
        $isFavorited = $favorite !== null;
    }
@endphp
<div class="bg-white rounded-xl shadow-sm {{ $isOwner ? 'border-2 border-indigo-400 ring-4 ring-indigo-50 transform scale-[1.02]' : 'border border-gray-200' }} hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative h-full flex flex-col group overflow-hidden">
    
    <!-- Top colored bar indicating PDF -->
    <div class="h-1.5 w-full bg-gradient-to-r from-red-500 to-rose-400"></div>

    @if($isOwner)
        <div class="absolute top-3 {{ app()->getLocale() === 'ar' ? 'start-3' : 'end-3' }} bg-indigo-100 border border-indigo-200 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm z-10 flex items-center gap-1">
            <x-heroicon name="sparkles" solid="true" class="w-3 h-3 text-indigo-500" /> {{ app()->getLocale() === 'ar' ? 'ملخصي' : 'My Summary' }}
        </div>
    @endif

    @if(auth()->check() && !$isOwner)
        <div class="absolute top-3 {{ app()->getLocale() === 'ar' ? 'start-3' : 'end-3' }} z-10 {{ $isFavorited ? 'opacity-100' : 'opacity-0 group-hover:opacity-100' }} transition-opacity duration-300">
            @if($isFavorited)
                <form action="{{ route('student.favorites.destroy', $favorite->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-1.5 rounded-full bg-white border border-red-100 hover:bg-red-50 text-red-500 transition-colors shadow-sm" title="{{ __('messages.student.favorites.remove') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                    </button>
                </form>
            @else
                <form action="{{ route('student.favorites.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="favoritable_id" value="{{ $note->id }}">
                    <input type="hidden" name="favoritable_type" value="note">
                    <button type="submit" class="p-1.5 rounded-full bg-white border border-gray-200 hover:border-red-200 hover:bg-red-50 text-gray-400 hover:text-red-500 transition-colors shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                    </button>
                </form>
            @endif
        </div>
    @endif

    <!-- Card Content -->
    <div class="p-5 flex-1 flex flex-col">
        <div class="flex items-start gap-4 mb-4">
            <!-- PDF Icon -->
            <div class="shrink-0 bg-red-50 p-2.5 rounded-xl border border-red-100 flex flex-col items-center justify-center shadow-sm">
                <svg class="w-7 h-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9h4" />
                    <text x="12" y="17" font-family="sans-serif" font-size="6" font-weight="bold" text-anchor="middle" fill="currentColor">PDF</text>
                </svg>
            </div>
            
            <div class="flex-1 min-w-0 pe-6">
                <h3 class="font-bold text-gray-800 text-base leading-snug mb-1.5 line-clamp-2" title="{{ $note->title }}">{{ $note->title }}</h3>
                <span class="text-[11px] font-medium text-indigo-700 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded-full inline-block truncate max-w-full" title="{{ $note->category?->faculty_name }} - {{ $note->category?->department_name }} ({{ $note->category?->study_year }})">
                    {{ $note->category?->faculty_name }} - {{ $note->category?->department_name }} ({{ $note->category?->study_year }})
                </span>
            </div>
        </div>

        @if($note->description)
            <p class="text-sm text-gray-500 line-clamp-2 mb-4 leading-relaxed">{{ $note->description }}</p>
        @else
            <div class="mb-4"></div>
        @endif
        
        <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
            <div class="flex items-center gap-2 truncate">
                <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center shrink-0 border border-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                </div>
                <span class="truncate font-medium text-gray-600">{{ $note->user?->name }}</span>
            </div>
            
            <a href="{{ route('users.ratings', $note->user) }}" class="flex items-center gap-1 text-yellow-600 hover:bg-yellow-50 px-2 py-1 rounded-lg transition border border-transparent hover:border-yellow-200 shrink-0">
                <span class="font-bold">{{ number_format($note->user?->average_rating ?? 0, 1) }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            </a>
        </div>
        
        <a href="{{ route('notes.show', $note) }}" class="mt-4 flex items-center justify-center gap-2 w-full bg-gray-50 border border-gray-200 text-gray-700 py-2.5 rounded-lg hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all font-medium text-sm group-hover:shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            {{ __('messages.notes.view_summary') }}
        </a>
    </div>
</div>
