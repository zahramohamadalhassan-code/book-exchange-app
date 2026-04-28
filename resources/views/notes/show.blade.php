@extends('layouts.app')
@section('title', $note->title . ' - الملخصات')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-sm border p-8">
        <div class="flex items-center gap-4 mb-6">
            <span class="text-5xl">📄</span>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $note->title }}</h1>
                <p class="text-gray-500">{{ $note->category?->department_name }} - {{ $note->category?->study_year }}</p>
            </div>
        </div>

        @if($note->description)
        <div class="mb-6">
            <h3 class="font-bold text-gray-700 mb-2">الوصف</h3>
            <p class="text-gray-600 leading-relaxed">{{ $note->description }}</p>
        </div>
        @endif

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500">الرافع</p>
                <p class="font-bold text-gray-800">{{ $note->user?->full_name }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500">تاريخ الرفع</p>
                <p class="font-bold text-gray-800">{{ $note->created_at->format('Y-m-d') }}</p>
            </div>
        </div>

        @if($note->pdf_file_url)
        <a href="{{ asset('storage/' . $note->pdf_file_url) }}" target="_blank"
           class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold text-lg hover:bg-indigo-700 transition inline-flex items-center gap-2">
            📥 تحميل الملخص (PDF)
        </a>
        @endif
    </div>
</div>
@endsection
