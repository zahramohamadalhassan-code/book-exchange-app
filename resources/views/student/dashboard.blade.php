@extends('layouts.app')
@section('title', __('messages.nav.my_dashboard') . ' - ' . __('messages.app_name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ __('messages.student.welcome') }} {{ auth()->user()->full_name }}</h1>
    <p class="text-gray-500 mb-8">{{ __('messages.student.overview_desc') }}</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border p-6 flex items-center gap-4">
            <div class="bg-indigo-100 text-indigo-600 p-3 rounded-xl">
                <x-heroicon name="book-open" class="w-7 h-7" />
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $booksCount }}</p>
                <p class="text-gray-500 text-sm">{{ __('messages.student.my_published_books') }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-6 flex items-center gap-4">
            <div class="bg-purple-100 text-purple-600 p-3 rounded-xl">
                <x-heroicon name="document-text" class="w-7 h-7" />
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $notesCount }}</p>
                <p class="text-gray-500 text-sm">{{ __('messages.student.my_summaries') }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-6 flex items-center gap-4">
            <div class="bg-orange-100 text-orange-600 p-3 rounded-xl">
                <x-heroicon name="clock" class="w-7 h-7" />
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $pendingRequests }}</p>
                <p class="text-gray-500 text-sm">{{ __('messages.student.pending_requests') }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('student.books.index') }}" class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition text-center group">
            <div class="inline-flex items-center justify-center bg-indigo-100 text-indigo-600 p-3 rounded-xl mb-3 group-hover:scale-110 transition-transform">
                <x-heroicon name="book-open" class="w-8 h-8" />
            </div>
            <p class="font-bold text-gray-800">{{ __('messages.student.my_books_link') }}</p>
            <p class="text-sm text-gray-500">{{ __('messages.student.my_books_desc') }}</p>
        </a>
        <a href="{{ route('student.notes.index') }}" class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition text-center group">
            <div class="inline-flex items-center justify-center bg-purple-100 text-purple-600 p-3 rounded-xl mb-3 group-hover:scale-110 transition-transform">
                <x-heroicon name="document-text" class="w-8 h-8" />
            </div>
            <p class="font-bold text-gray-800">{{ __('messages.student.my_notes_link') }}</p>
            <p class="text-sm text-gray-500">{{ __('messages.student.my_notes_desc') }}</p>
        </a>
        <a href="{{ route('student.transactions.index') }}" class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition text-center group">
            <div class="inline-flex items-center justify-center bg-green-100 text-green-600 p-3 rounded-xl mb-3 group-hover:scale-110 transition-transform">
                <x-heroicon name="arrow-path" class="w-8 h-8" />
            </div>
            <p class="font-bold text-gray-800">{{ __('messages.student.my_transactions') }}</p>
            <p class="text-sm text-gray-500">{{ __('messages.student.my_transactions_desc') }}</p>
        </a>
        <a href="{{ route('student.favorites.index') }}" class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition text-center group">
            <div class="inline-flex items-center justify-center bg-red-100 text-red-500 p-3 rounded-xl mb-3 group-hover:scale-110 transition-transform">
                <x-heroicon name="heart" class="w-8 h-8" />
            </div>
            <p class="font-bold text-gray-800">{{ __('messages.student.my_favorites') }}</p>
            <p class="text-sm text-gray-500">{{ __('messages.student.my_favorites_desc') }}</p>
        </a>
    </div>
</div>
@endsection
