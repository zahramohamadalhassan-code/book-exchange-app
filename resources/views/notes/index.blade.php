@extends('layouts.app')
@section('title', 'الملخصات الرقمية - منصة تبادل الكتب')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">📝 الملخصات الرقمية</h1>

    {{-- بحث وفلترة --}}
    <form method="GET" action="{{ route('notes.browse') }}" class="flex flex-wrap gap-3 items-center mb-8">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن ملخص..."
               class="border border-gray-300 rounded-lg px-4 py-2 flex-1 min-w-[200px] focus:ring-2 focus:ring-indigo-500">
        <select name="category_id" class="border border-gray-300 rounded-lg px-3 py-2">
            <option value="">كل الأقسام</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->department_name }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">بحث</button>
    </form>

    @if($notes->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($notes as $note)
        <div class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition">
            <div class="flex items-center gap-3 mb-3">
                <span class="text-3xl">📄</span>
                <div>
                    <h3 class="font-bold text-gray-800 line-clamp-1">{{ $note->title }}</h3>
                    <p class="text-xs text-gray-400">{{ $note->category?->department_name }}</p>
                </div>
            </div>
            @if($note->description)
                <p class="text-sm text-gray-500 line-clamp-2 mb-3">{{ $note->description }}</p>
            @endif
            <p class="text-xs text-gray-400 mb-3">بواسطة: {{ $note->user?->full_name }}</p>
            <a href="{{ route('notes.show', $note) }}" class="block text-center bg-indigo-50 text-indigo-600 py-2 rounded-lg hover:bg-indigo-100 transition font-medium text-sm">
                عرض الملخص
            </a>
        </div>
        @endforeach
    </div>
    <div class="mt-8">{{ $notes->appends(request()->query())->links() }}</div>
    @else
    <div class="text-center py-16 text-gray-400">
        <span class="text-6xl block mb-4">📭</span>
        <p class="text-xl">لا توجد ملخصات متاحة</p>
    </div>
    @endif
</div>
@endsection
