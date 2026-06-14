@extends('layouts.app')
@section('title', __('messages.student.notes.title') . ' - ' . __('messages.app_name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="{ deleteModalOpen: false, deleteUrl: '', itemTitle: '' }">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2"><x-heroicon name="document-text" class="w-8 h-8 text-purple-600" /> {{ __('messages.student.notes.title') }}</h1>
        <a href="{{ route('student.notes.create') }}" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition font-medium inline-flex items-center gap-1.5"><x-heroicon name="plus" class="w-4 h-4" /> {{ __('messages.student.notes.upload_summary') }}</a>
    </div>

    <form method="GET" action="{{ route('student.notes.index') }}" class="bg-white rounded-xl shadow-sm border p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.student.notes.search_placeholder') }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <select name="category_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">{{ __('messages.student.notes.all_categories') }}</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->department_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="moderation_status" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">{{ __('messages.student.notes.all_statuses') }}</option>
                    <option value="approved" {{ request('moderation_status') == 'approved' ? 'selected' : '' }}>{{ __('messages.moderation.approved') }}</option>
                    <option value="rejected" {{ request('moderation_status') == 'rejected' ? 'selected' : '' }}>{{ __('messages.moderation.rejected') }}</option>
                    <option value="pending" {{ request('moderation_status') == 'pending' ? 'selected' : '' }}>{{ __('messages.moderation.under_review') }}</option>
                </select>
            </div>
            <div>
                <select name="sort" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>{{ __('messages.student.notes.newest') }}</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>{{ __('messages.student.notes.oldest') }}</option>
                    <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>{{ __('messages.student.notes.title_asc') }}</option>
                    <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>{{ __('messages.student.notes.title_desc') }}</option>
                </select>
            </div>
        </div>
        <div class="flex items-center gap-2 mt-3">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">{{ __('messages.student.notes.apply') }}</button>
            <a href="{{ route('student.notes.index') }}" class="text-gray-500 text-sm hover:underline">{{ __('messages.student.notes.clear_filters') }}</a>
        </div>
    </form>

    @if($notes->count() > 0)
    <p class="text-sm text-gray-500 mb-3">{{ __('messages.student.notes.results_count', ['count' => $notes->total()]) }}</p>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($notes as $note)
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold text-gray-800">{{ $note->title }}</h3>
                <span class="text-xs px-2 py-1 rounded-full {{ $note->moderation_status === 'approved' ? 'bg-green-100 text-green-700' : ($note->moderation_status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                    {{ $note->moderation_status === 'approved' ? __('messages.moderation.approved') : ($note->moderation_status === 'rejected' ? __('messages.moderation.rejected') : __('messages.moderation.under_review')) }}
                </span>
            </div>
            <p class="text-sm text-gray-500 line-clamp-2 mb-3">{{ $note->description ?? __('messages.student.notes.no_description') }}</p>
            <p class="text-xs text-gray-400 mb-3">{{ $note->category?->department_name }}</p>
            <div class="flex gap-2">
                @if($note->pdf_file_url)
                <a href="{{ asset('storage/' . $note->pdf_file_url) }}" target="_blank" class="text-indigo-600 text-sm hover:underline inline-flex items-center gap-1"><x-heroicon name="arrow-down-tray" class="w-4 h-4" /> {{ __('messages.student.notes.download') }}</a>
                @endif
                <a href="{{ route('student.notes.edit', $note) }}" class="text-blue-600 text-sm hover:underline">{{ __('messages.student.notes.edit_btn') }}</a>
                <button type="button" @click="deleteModalOpen = true; deleteUrl = '{{ route('student.notes.destroy', $note) }}'; itemTitle = '{{ addslashes($note->title) }}'" class="text-red-500 text-sm hover:underline">{{ __('messages.student.notes.delete_btn') }}</button>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $notes->links() }}</div>
    @else
    <div class="text-center py-16 bg-white rounded-xl border">
        <span class="text-5xl block mb-3">📭</span>
        <p class="text-gray-500 mb-4">{{ __('messages.student.notes.no_notes') }}</p>
        <a href="{{ route('student.notes.create') }}" class="bg-indigo-600 text-white px-5 py-2 rounded-lg">{{ __('messages.student.notes.upload_first') }}</a>
    </div>
    @endif
    {{-- Delete Confirmation Modal --}}
    <x-confirm-delete-modal />
</div>
@endsection
