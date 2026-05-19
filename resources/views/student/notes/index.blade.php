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
    <!-- Delete Confirmation Modal -->
    <div x-show="deleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="deleteModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="deleteModalOpen = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="deleteModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-start overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 rtl:text-end">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ms-4 rtl:sm:me-4 rtl:sm:ms-0 sm:text-start w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            {{ app()->getLocale() === 'ar' ? 'تأكيد الحذف' : 'Confirm Deletion' }}
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                {{ app()->getLocale() === 'ar' ? 'هل أنت متأكد أنك تريد حذف' : 'Are you sure you want to delete' }} 
                                <span class="font-bold text-gray-800" x-text="itemTitle"></span>؟ 
                                {{ app()->getLocale() === 'ar' ? 'لا يمكن التراجع عن هذا الإجراء.' : 'This action cannot be undone.' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <form method="POST" :action="deleteUrl">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ms-3 rtl:sm:me-3 rtl:sm:ms-0 sm:w-auto sm:text-sm">
                            {{ app()->getLocale() === 'ar' ? 'حذف' : 'Delete' }}
                        </button>
                    </form>
                    <button type="button" @click="deleteModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
