@extends('layouts.app')
@section('title', __('messages.student.books.edit.title') . $book->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('student.books.index') }}" class="text-gray-500 hover:text-indigo-600">
            <x-heroicon name="{{ app()->getLocale() === 'ar' ? 'arrow-right' : 'arrow-left' }}" class="w-6 h-6" />
        </a>
        <h1 class="text-3xl font-bold text-gray-800">{{ __('messages.student.books.edit.title') }} {{ $book->title }}</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border p-8">
        <form method="POST" action="{{ route('student.books.update', $book) }}" x-data="{ offerType: '{{ old('offer_type', $book->offer_type) }}' }">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.student.books.create.book_title') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $book->title) }}" required class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('title') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.student.books.edit.availability') }} <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="available" {{ old('status', $book->status) == 'available' ? 'selected' : '' }}>{{ __('messages.student.books.edit.available') }}</option>
                        <option value="pending" {{ old('status', $book->status) == 'pending' ? 'selected' : '' }}>{{ __('messages.student.books.edit.pending') }}</option>
                        <option value="sold" {{ old('status', $book->status) == 'sold' ? 'selected' : '' }}>{{ __('messages.student.books.edit.sold') }}</option>
                    </select>
                    @error('status') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.student.books.create.book_condition') }} <span class="text-red-500">*</span></label>
                    <select name="condition" required class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="excellent" {{ old('condition', $book->condition) == 'excellent' ? 'selected' : '' }}>{{ __('messages.student.books.create.excellent_new') }}</option>
                        <option value="good" {{ old('condition', $book->condition) == 'good' ? 'selected' : '' }}>{{ __('messages.student.books.create.good_used') }}</option>
                        <option value="fair" {{ old('condition', $book->condition) == 'fair' ? 'selected' : '' }}>{{ __('messages.student.books.create.fair_marks') }}</option>
                        <option value="poor" {{ old('condition', $book->condition) == 'poor' ? 'selected' : '' }}>{{ __('messages.student.books.create.poor_worn') }}</option>
                    </select>
                    @error('condition') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.student.books.create.pages_count') }}</label>
                    <input type="number" name="pages_count" value="{{ old('pages_count', $book->pages_count) }}" min="1" placeholder="{{ __('messages.student.books.create.pages_count_placeholder') }}" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('pages_count') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.student.books.create.offer_type') }} <span class="text-red-500">*</span></label>
                    <select name="offer_type" x-model="offerType" required class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="sale">{{ __('messages.student.books.create.sale') }}</option>
                        <option value="exchange">{{ __('messages.student.books.create.exchange') }}</option>
                        <option value="donate">{{ __('messages.student.books.create.donation') }}</option>
                    </select>
                    @error('offer_type') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div x-show="offerType === 'sale'" x-transition class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.student.books.create.price_syp') }} <span class="text-red-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price', $book->price) }}" step="0.01" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('price') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div x-show="offerType === 'exchange'" x-transition class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.student.books.create.exchange_for') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="exchange_for" value="{{ old('exchange_for', $book->exchange_for) }}" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('exchange_for') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end pt-6 border-t">
                <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold text-lg hover:bg-indigo-700 transition">{{ __('messages.student.books.edit.save_changes') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
