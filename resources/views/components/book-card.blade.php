@php
    $isOwner = auth()->check() && auth()->id() === $book->user_id;
    $favorite = null;
    $isFavorited = false;
    if (auth()->check() && !$isOwner) {
        $favorite = auth()->user()->favorites()
            ->where('favoritable_id', $book->id)
            ->where('favoritable_type', App\Models\Book::class)
            ->first();
        $isFavorited = $favorite !== null;
    }
@endphp

<div class="bg-white rounded-xl shadow-sm {{ $isOwner ? 'border-2 border-indigo-400 ring-4 ring-indigo-50 transform scale-[1.02]' : 'border border-gray-100' }} overflow-hidden hover:shadow-md transition-all duration-300 group relative">
    @if($isOwner)
        <div class="absolute top-0 {{ app()->getLocale() === 'ar' ? 'start-0 rounded-br-xl' : 'end-0 rounded-bl-xl' }} bg-indigo-500 text-white text-xs font-bold px-3 py-1 z-20 shadow-sm">
            {{ __('messages.books.my_book') }} <x-heroicon name="sparkles" solid="true" class="w-3.5 h-3.5 inline" />
        </div>
    @endif

    <div class="relative h-48 bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center overflow-hidden">
        @if(auth()->check() && !$isOwner)
            <div class="absolute top-3 {{ app()->getLocale() === 'ar' ? 'start-3' : 'end-3' }} z-20">
                @if($isFavorited)
                    <form action="{{ route('student.favorites.destroy', $favorite->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 rounded-full bg-white/90 hover:bg-white text-red-500 transition-colors shadow-sm" title="{{ __('messages.student.favorites.remove') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                        </button>
                    </form>
                @else
                    <form action="{{ route('student.favorites.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="favoritable_id" value="{{ $book->id }}">
                        <input type="hidden" name="favoritable_type" value="book">
                        <button type="submit" class="p-2 rounded-full bg-white/90 hover:bg-white text-gray-400 hover:text-red-500 transition-colors shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                        </button>
                    </form>
                @endif
            </div>
        @endif

        @if($book->cover_image_url)
            <img src="{{ asset('storage/' . $book->cover_image_url) }}" alt="{{ $book->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="flex justify-center"><x-heroicon name="book-open" class="w-16 h-16 text-indigo-300" /></div>
        @endif

        <span class="absolute top-3 {{ app()->getLocale() === 'ar' ? 'end-3' : 'start-3' }} z-10 px-3 py-1 rounded-full text-xs font-bold text-white
            {{ $book->offer_type === 'sale' ? 'bg-green-500' : ($book->offer_type === 'exchange' ? 'bg-blue-500' : 'bg-orange-500') }}">
            {{ $book->offer_type === 'sale' ? __('messages.offer_types.sale') : ($book->offer_type === 'exchange' ? __('messages.offer_types.exchange') : __('messages.offer_types.donate')) }}
        </span>
    </div>

    <div class="p-4">
        <h3 class="font-bold text-gray-800 text-lg mb-1 line-clamp-1">{{ $book->title }}</h3>

        @if($book->author)
            <p class="text-gray-500 text-sm mb-2">{{ $book->author }}</p>
        @endif

        <div class="flex items-center justify-between mt-3">
            @if($book->offer_type === 'sale' && $book->price)
                <span class="text-indigo-600 font-bold text-lg">{{ number_format($book->price) }} SYP</span>
            @elseif($book->offer_type === 'donate')
                <span class="text-orange-500 font-bold inline-flex items-center gap-1">{{ __('messages.books.free') }} <x-heroicon name="gift" class="w-4 h-4" /></span>
            @else
                <div class="flex flex-col">
                    <span class="text-blue-500 font-bold inline-flex items-center gap-1">{{ __('messages.books.exchange') }} <x-heroicon name="arrow-path" class="w-4 h-4" /></span>
                    @if($book->exchange_for)
                        <span class="text-xs text-gray-500 line-clamp-1" title="{{ $book->exchange_for }}">{{ __('messages.books.requested') }}: {{ $book->exchange_for }}</span>
                    @endif
                </div>
            @endif

            <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600">
                {{ $book->condition === 'excellent' ? __('messages.conditions.excellent') : ($book->condition === 'good' ? __('messages.conditions.good') : ($book->condition === 'fair' ? __('messages.conditions.fair') : __('messages.conditions.poor'))) }}
            </span>
        </div>

        @if($book->category)
            <p class="text-xs text-gray-400 mt-2">{{ $book->category?->faculty_name }} - {{ $book->category?->study_year }}</p>
        @endif

        <div class="flex items-center justify-between mt-3 text-xs text-gray-500">
            <span>{{ __('messages.by') }} {{ $book->user?->full_name }}</span>
            <a href="{{ route('users.ratings', $book->user) }}" class="flex items-center text-yellow-500 hover:text-yellow-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ app()->getLocale() === 'ar' ? 'ms-1' : 'me-1' }}" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <span class="font-bold hover:underline">{{ number_format($book->user?->average_rating ?? 0, 1) }}</span>
            </a>
        </div>

        <a href="{{ route('books.show', $book) }}"
           class="mt-3 block text-center bg-indigo-50 text-indigo-600 py-2 rounded-lg hover:bg-indigo-100 transition font-medium text-sm">
            {{ __('messages.books.view_details') }}
        </a>
    </div>
</div>
