@extends('layouts.app')

@section('title', __('messages.tagline') . ' - ' . __('messages.nav.home'))

@section('content')
<section class="bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4 leading-tight">
                {{ __('messages.home.hero_title') }}
            </h1>
        <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">
            {{ __('messages.home.hero_desc') }}
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('books.browse') }}" class="bg-white text-indigo-600 px-8 py-3 rounded-xl font-bold text-lg hover:bg-indigo-50 transition shadow-lg">
                {{ __('messages.home.browse_books') }}
            </a>
            @guest
            <a href="{{ route('register') }}" class="border-2 border-white text-white px-8 py-3 rounded-xl font-bold text-lg hover:bg-white/10 transition">
                {{ __('messages.home.join_now') }}
            </a>
            @endguest
        </div>
    </div>
</section>

<section class="py-10 -mt-10">
    <div class="max-w-5xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-xl p-6 grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
            <div>
                <div class="text-3xl font-extrabold text-indigo-600">{{ $stats['books_count'] }}</div>
                <div class="text-gray-500 mt-1">{{ __('messages.home.books_available') }}</div>
            </div>
            <div>
                <div class="text-3xl font-extrabold text-purple-600">{{ $stats['notes_count'] }}</div>
                <div class="text-gray-500 mt-1">{{ __('messages.home.digital_summaries') }}</div>
            </div>
            <div>
                <div class="text-3xl font-extrabold text-green-600">{{ $stats['users_count'] }}</div>
                <div class="text-gray-500 mt-1">{{ __('messages.home.students_registered') }}</div>
            </div>
        </div>
    </div>
</section>

<section class="py-12">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2"><x-heroicon name="book-open" class="w-7 h-7 text-indigo-600" /> {{ __('messages.home.latest_books') }}</h2>
            <a href="{{ route('books.browse') }}" class="text-indigo-600 hover:underline font-medium">{{ __('messages.home.view_all') }} {{ app()->getLocale() === 'ar' ? '←' : '→' }}</a>
        </div>

        @if($latestBooks->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($latestBooks as $book)
                @include('components.book-card', ['book' => $book])
            @endforeach
        </div>
        @else
        <div class="text-center py-12 text-gray-400">
            <div class="flex justify-center mb-3"><x-heroicon name="inbox" class="w-16 h-16" /></div>
            <p>{{ __('messages.home.no_books') }}</p>
        </div>
        @endif
    </div>
</section>

<section class="py-12 bg-gray-100">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2"><x-heroicon name="document-text" class="w-7 h-7 text-purple-600" /> {{ __('messages.home.latest_notes') }}</h2>
            <a href="{{ route('notes.browse') }}" class="text-indigo-600 hover:underline font-medium">{{ __('messages.home.view_all') }} {{ app()->getLocale() === 'ar' ? '←' : '→' }}</a>
        </div>

        @if($latestNotes->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($latestNotes as $note)
                @include('components.note-card', ['note' => $note])
            @endforeach
        </div>
        @else
        <div class="text-center py-12 text-gray-400">
            <div class="flex justify-center mb-3"><x-heroicon name="inbox" class="w-16 h-16" /></div>
            <p>{{ __('messages.home.no_notes') }}</p>
        </div>
        @endif
    </div>
</section>

<section class="py-16 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-center">
    <div class="max-w-3xl mx-auto px-4">
        <h2 class="text-3xl font-bold mb-4">{{ __('messages.home.cta_title') }}</h2>
        <p class="text-lg text-purple-100 mb-6">{{ __('messages.home.cta_desc') }}</p>
        @guest
        <a href="{{ route('register') }}" class="bg-white text-indigo-600 px-8 py-3 rounded-xl font-bold text-lg hover:bg-indigo-50 transition inline-block">
            {{ __('messages.home.create_free_account') }}
        </a>
        @else
        <a href="{{ route('student.books.create') }}" class="bg-white text-indigo-600 px-8 py-3 rounded-xl font-bold text-lg hover:bg-indigo-50 transition inline-block">
            {{ __('messages.home.add_your_book') }}
        </a>
        @endguest
    </div>
</section>
@endsection
