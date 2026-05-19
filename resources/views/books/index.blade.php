@extends('layouts.app')
@section('title', __('messages.books.browse_books') . ' - ' . __('messages.app_name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 flex items-center gap-2"><x-heroicon name="book-open" class="w-8 h-8 text-indigo-600" /> {{ __('messages.books.browse_books') }}</h1>

    <div x-data="{ showFilters: false }" class="mb-8">
        <div class="flex flex-wrap gap-4 items-center">
            <form method="GET" action="{{ route('books.browse') }}" class="flex flex-wrap gap-3 items-center flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.books.search_placeholder') }}"
                       class="border border-gray-300 rounded-lg px-4 py-2 flex-1 min-w-[200px] focus:ring-2 focus:ring-indigo-500 focus:border-transparent">

                <select name="offer_type" class="border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">{{ __('messages.books.all_types') }}</option>
                    <option value="sale" {{ request('offer_type') == 'sale' ? 'selected' : '' }}>{{ __('messages.books.for_sale') }}</option>
                    <option value="exchange" {{ request('offer_type') == 'exchange' ? 'selected' : '' }}>{{ __('messages.books.for_exchange') }}</option>
                    <option value="donate" {{ request('offer_type') == 'donate' ? 'selected' : '' }}>{{ __('messages.books.donate') }}</option>
                </select>

                <select name="condition" class="border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">{{ __('messages.books.all_conditions') }}</option>
                    <option value="excellent" {{ request('condition') == 'excellent' ? 'selected' : '' }}>{{ __('messages.books.excellent') }}</option>
                    <option value="good" {{ request('condition') == 'good' ? 'selected' : '' }}>{{ __('messages.books.good') }}</option>
                    <option value="fair" {{ request('condition') == 'fair' ? 'selected' : '' }}>{{ __('messages.books.fair') }}</option>
                    <option value="poor" {{ request('condition') == 'poor' ? 'selected' : '' }}>{{ __('messages.books.poor') }}</option>
                </select>

                <select name="category_id" class="border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">{{ __('messages.books.all_departments') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->faculty_name }} - {{ $cat->department_name }} ({{ $cat->study_year }})
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">{{ __('messages.books.search') }}</button>

                @if(request()->hasAny(['search', 'offer_type', 'condition', 'category_id']))
                    <a href="{{ route('books.browse') }}" class="text-red-500 hover:text-red-700 text-sm">{{ __('messages.books.clear_filters') }}</a>
                @endif
            </form>
        </div>
    </div>

    @if($books->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($books as $book)
            @include('components.book-card', ['book' => $book])
        @endforeach
    </div>

    <div class="mt-8">
        {{ $books->appends(request()->query())->links() }}
    </div>
    @else
    <div class="text-center py-16 text-gray-400">
        <div class="flex justify-center mb-4"><x-heroicon name="magnifying-glass" class="w-16 h-16 text-gray-300" /></div>
        <p class="text-xl">{{ __('messages.books.no_results') }}</p>
        <a href="{{ route('books.browse') }}" class="text-indigo-600 hover:underline mt-2 inline-block">{{ __('messages.books.show_all') }}</a>
    </div>
    @endif
</div>
@endsection
