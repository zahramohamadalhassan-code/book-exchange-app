@extends('layouts.app')
@section('title', __('messages.notes.digital_summaries') . ' - ' . __('messages.app_name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 flex items-center gap-2"><x-heroicon name="document-text" class="w-8 h-8 text-purple-600" /> {{ __('messages.notes.digital_summaries') }}</h1>

    <form method="GET" action="{{ route('notes.browse') }}" class="flex flex-wrap gap-3 items-center mb-8">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.notes.search_placeholder') }}"
               class="border border-gray-300 rounded-lg px-4 py-2 flex-1 min-w-[200px] focus:ring-2 focus:ring-indigo-500">
        <select name="category_id" class="border border-gray-300 rounded-lg px-3 py-2">
            <option value="">{{ __('messages.notes.all_departments') }}</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->faculty_name }} - {{ $cat->department_name }} ({{ $cat->study_year }})</option>
            @endforeach
        </select>
        <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">{{ __('messages.notes.search') }}</button>
    </form>

    @if($notes->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($notes as $note)
            @include('components.note-card', ['note' => $note])
        @endforeach
    </div>
    <div class="mt-8">{{ $notes->appends(request()->query())->links() }}</div>
    @else
    <div class="text-center py-16 text-gray-400">
        <div class="flex justify-center mb-4"><x-heroicon name="inbox" class="w-16 h-16 text-gray-300" /></div>
        <p class="text-xl">{{ __('messages.notes.no_summaries') }}</p>
    </div>
    @endif
</div>
@endsection
