@extends('layouts.app')
@section('title', __('messages.student.books.title') . ' - ' . __('messages.app_name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="{ deleteModalOpen: false, deleteUrl: '', itemTitle: '' }">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2"><x-heroicon name="book-open" class="w-8 h-8 text-indigo-600" /> {{ __('messages.student.books.title') }}</h1>
        <a href="{{ route('student.books.create') }}" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition font-medium inline-flex items-center gap-1.5"><x-heroicon name="plus" class="w-4 h-4" /> {{ __('messages.student.books.add_book') }}</a>
    </div>

    <form method="GET" action="{{ route('student.books.index') }}" class="bg-white rounded-xl shadow-sm border p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <div class="lg:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.student.books.search_placeholder') }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <select name="offer_type" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">{{ __('messages.student.books.all_types') }}</option>
                    <option value="sale" {{ request('offer_type') == 'sale' ? 'selected' : '' }}>{{ __('messages.offer_types.sale') }}</option>
                    <option value="exchange" {{ request('offer_type') == 'exchange' ? 'selected' : '' }}>{{ __('messages.offer_types.exchange') }}</option>
                    <option value="donate" {{ request('offer_type') == 'donate' ? 'selected' : '' }}>{{ __('messages.offer_types.donate') }}</option>
                </select>
            </div>
            <div>
                <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">{{ __('messages.student.books.all_statuses') }}</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>{{ __('messages.statuses.available') }}</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('messages.statuses.pending') }}</option>
                    <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>{{ __('messages.statuses.sold') }}</option>
                </select>
            </div>
            <div>
                <select name="moderation_status" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">{{ __('messages.student.books.all_approvals') }}</option>
                    <option value="approved" {{ request('moderation_status') == 'approved' ? 'selected' : '' }}>{{ __('messages.moderation.approved') }}</option>
                    <option value="rejected" {{ request('moderation_status') == 'rejected' ? 'selected' : '' }}>{{ __('messages.moderation.rejected') }}</option>
                    <option value="pending" {{ request('moderation_status') == 'pending' ? 'selected' : '' }}>{{ __('messages.moderation.under_review') }}</option>
                </select>
            </div>
            <div>
                <select name="sort" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>{{ __('messages.student.books.newest') }}</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>{{ __('messages.student.books.oldest') }}</option>
                    <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>{{ __('messages.student.books.title_asc') }}</option>
                    <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>{{ __('messages.student.books.title_desc') }}</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>{{ __('messages.student.books.price_asc') }}</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>{{ __('messages.student.books.price_desc') }}</option>
                </select>
            </div>
        </div>
        @if(request('category_id'))
            <input type="hidden" name="category_id" value="{{ request('category_id') }}">
        @endif
        <div class="flex items-center gap-2 mt-3">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">{{ __('messages.student.books.apply') }}</button>
            <a href="{{ route('student.books.index') }}" class="text-gray-500 text-sm hover:underline">{{ __('messages.student.books.clear_filters') }}</a>
        </div>
    </form>

    @if($books->count() > 0)
    <p class="text-sm text-gray-500 mb-3">{{ __('messages.student.books.results_count', ['count' => $books->total()]) }}</p>
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <table class="w-full {{ app()->getLocale() === 'ar' ? 'text-end' : 'text-start' }}">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-sm font-medium text-gray-500">{{ __('messages.student.books.table_title') }}</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-500">{{ __('messages.student.books.table_type') }}</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-500">{{ __('messages.student.books.table_status') }}</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-500">{{ __('messages.student.books.table_approval') }}</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-500">{{ __('messages.student.books.table_price') }}</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-500">{{ __('messages.student.books.table_actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($books as $book)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $book->title }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-1 rounded-full {{ $book->offer_type === 'sale' ? 'bg-green-100 text-green-700' : ($book->offer_type === 'exchange' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700') }}">
                            {{ $book->offer_type === 'sale' ? __('messages.offer_types.sale') : ($book->offer_type === 'exchange' ? __('messages.offer_types.exchange') : __('messages.offer_types.donate')) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <span class="text-xs px-2 py-1 rounded-full {{ $book->status === 'available' ? 'bg-green-100 text-green-700' : ($book->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">
                            {{ __('messages.statuses.' . $book->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-1 rounded-full {{ $book->moderation_status === 'approved' ? 'bg-green-100 text-green-700' : ($book->moderation_status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ $book->moderation_status === 'approved' ? __('messages.moderation.approved') : ($book->moderation_status === 'rejected' ? __('messages.moderation.rejected') : __('messages.moderation.under_review')) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">{{ $book->price ? number_format($book->price) . ' SYP' : '-' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('student.books.edit', $book) }}" class="text-indigo-600 hover:underline text-sm">{{ __('messages.student.books.edit_btn') }}</a>
                            <button type="button" @click="deleteModalOpen = true; deleteUrl = '{{ route('student.books.destroy', $book) }}'; itemTitle = '{{ addslashes($book->title) }}'" class="text-red-500 hover:underline text-sm">{{ __('messages.student.books.delete_btn') }}</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $books->links() }}</div>
    @else
    <div class="text-center py-16 bg-white rounded-xl border">
        <span class="text-5xl block mb-3">📭</span>
        <p class="text-gray-500 mb-4">{{ __('messages.student.books.no_books') }}</p>
        <a href="{{ route('student.books.create') }}" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700">{{ __('messages.student.books.add_first_book') }}</a>
    </div>
    @endif
    {{-- Delete Confirmation Modal --}}
    <x-confirm-delete-modal />
</div>
@endsection
