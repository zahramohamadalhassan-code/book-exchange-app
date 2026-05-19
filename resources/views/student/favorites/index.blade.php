@extends('layouts.app')
@section('title', __('messages.student.favorites.title') . ' - ' . __('messages.app_name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 flex items-center gap-2"><x-heroicon name="heart" class="w-8 h-8 text-red-500" /> {{ __('messages.student.favorites.title') }}</h1>

    @if($favorites->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($favorites as $fav)
        <div class="bg-white rounded-xl shadow-sm border p-5 flex items-start justify-between hover:shadow-md transition">
            <a href="{{ $fav->favoritable ? ($fav->favoritable instanceof \App\Models\Book ? route('books.show', $fav->favoritable) : route('notes.show', $fav->favoritable)) : '#' }}" class="flex items-center gap-3 flex-1 group">
                <span class="flex-shrink-0 text-indigo-500 group-hover:scale-110 transition-transform">
                    <x-heroicon name="{{ $fav->favoritable instanceof \App\Models\Book ? 'book-open' : 'document-text' }}" class="w-8 h-8" />
                </span>
                <div>
                    <h3 class="font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">{{ $fav->favoritable?->title ?? __('messages.student.favorites.deleted_item') }}</h3>
                    <p class="text-xs text-gray-400">
                        {{ $fav->favoritable instanceof \App\Models\Book ? __('messages.student.favorites.book') : __('messages.student.favorites.summary') }}
                    </p>
                </div>
            </a>
            <form method="POST" action="{{ route('student.favorites.destroy', $fav) }}" class="ms-4">
                @csrf @method('DELETE')
                <button class="text-red-400 hover:text-red-600 transition" title="{{ __('messages.student.favorites.remove') }}"><x-heroicon name="trash" class="w-5 h-5" /></button>
            </form>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-xl border">
        <div class="flex justify-center mb-3"><x-heroicon name="heart" class="w-16 h-16 text-gray-300" /></div>
        <p class="text-gray-500">{{ __('messages.student.favorites.no_favorites') }}</p>
        <a href="{{ route('books.browse') }}" class="text-indigo-600 hover:underline mt-2 inline-block">{{ __('messages.student.favorites.browse_books') }}</a>
    </div>
    @endif
</div>
@endsection
