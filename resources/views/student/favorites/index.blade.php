@extends('layouts.app')
@section('title', 'المفضلة - منصة تبادل الكتب')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">❤️ المفضلة</h1>

    @if($favorites->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($favorites as $fav)
        <div class="bg-white rounded-xl shadow-sm border p-5 flex items-start justify-between">
            <div class="flex items-center gap-3">
                <span class="text-3xl">{{ $fav->favoritable instanceof \App\Models\Book ? '📖' : '📄' }}</span>
                <div>
                    <h3 class="font-bold text-gray-800">{{ $fav->favoritable?->title ?? 'عنصر محذوف' }}</h3>
                    <p class="text-xs text-gray-400">
                        {{ $fav->favoritable instanceof \App\Models\Book ? 'كتاب' : 'ملخص' }}
                    </p>
                </div>
            </div>
            <form method="POST" action="{{ route('student.favorites.destroy', $fav) }}">
                @csrf @method('DELETE')
                <button class="text-red-400 hover:text-red-600 text-xl" title="إزالة من المفضلة">🗑️</button>
            </form>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-xl border">
        <span class="text-5xl block mb-3">💔</span>
        <p class="text-gray-500">لم تضف أي عنصر للمفضلة بعد</p>
        <a href="{{ route('books.browse') }}" class="text-indigo-600 hover:underline mt-2 inline-block">تصفح الكتب</a>
    </div>
    @endif
</div>
@endsection
