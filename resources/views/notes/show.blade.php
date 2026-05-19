@extends('layouts.app')
@section('title', $note->title . ' - ' . __('messages.notes.digital_summaries'))

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-sm border p-8">
        <div class="flex items-center gap-4 mb-6">
            <div class="text-indigo-500">
                <x-heroicon name="document-text" class="w-12 h-12" />
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $note->title }}</h1>
                <p class="text-gray-500">{{ $note->category?->faculty_name }} - {{ $note->category?->study_year }}</p>
            </div>
        </div>

        @if($note->description)
        <div class="mb-6">
            <h3 class="font-bold text-gray-700 mb-2">{{ __('messages.notes.description') }}</h3>
            <p class="text-gray-600 leading-relaxed">{{ $note->description }}</p>
        </div>
        @endif

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500">{{ __('messages.notes.uploader') }}</p>
                <div class="flex items-center justify-between">
                    <p class="font-bold text-gray-800">{{ $note->user?->full_name }}</p>
                    <a href="{{ route('users.ratings', $note->user) }}" class="flex items-center text-yellow-500 hover:text-yellow-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ app()->getLocale() === 'ar' ? 'me-1' : 'ms-1' }}" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <span class="font-bold text-base hover:underline">{{ number_format($note->user?->average_rating ?? 0, 1) }}</span>
                    </a>
                </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500">{{ __('messages.notes.upload_date') }}</p>
                <p class="font-bold text-gray-800">{{ $note->created_at->format('Y-m-d') }}</p>
            </div>
        </div>

        @if($note->pdf_file_url)
        <a href="{{ asset('storage/' . $note->pdf_file_url) }}" target="_blank"
           class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold text-lg hover:bg-indigo-700 transition inline-flex items-center gap-2">
            <x-heroicon name="arrow-down-tray" class="w-5 h-5" /> {{ __('messages.notes.download_pdf') }}
        </a>
        @endif
    </div>
</div>
@endsection
