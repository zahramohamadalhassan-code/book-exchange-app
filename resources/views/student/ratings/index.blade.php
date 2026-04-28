@extends('layouts.app')
@section('title', 'التقييمات - منصة تبادل الكتب')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">⭐ التقييمات</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- التقييمات المستلمة --}}
        <div>
            <h2 class="text-xl font-bold text-gray-700 mb-4">📥 تقييمات حصلت عليها</h2>
            @if($receivedRatings->count() > 0)
                @foreach($receivedRatings as $rating)
                <div class="bg-white rounded-xl shadow-sm border p-4 mb-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-medium text-gray-800">{{ $rating->reviewer?->full_name }}</p>
                        <div class="text-yellow-500">
                            @for($i = 1; $i <= 5; $i++)
                                {{ $i <= $rating->stars ? '★' : '☆' }}
                            @endfor
                        </div>
                    </div>
                    @if($rating->comment)
                        <p class="text-sm text-gray-500">{{ $rating->comment }}</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-2">{{ $rating->created_at->diffForHumans() }}</p>
                </div>
                @endforeach
            @else
                <p class="text-gray-400 text-center py-8">لم تحصل على تقييمات بعد</p>
            @endif
        </div>

        {{-- التقييمات المرسلة --}}
        <div>
            <h2 class="text-xl font-bold text-gray-700 mb-4">📤 تقييمات كتبتها</h2>
            @if($givenRatings->count() > 0)
                @foreach($givenRatings as $rating)
                <div class="bg-white rounded-xl shadow-sm border p-4 mb-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-medium text-gray-800">لـ: {{ $rating->reviewedUser?->full_name }}</p>
                        <div class="flex items-center gap-2">
                            <span class="text-yellow-500">
                                @for($i = 1; $i <= 5; $i++)
                                    {{ $i <= $rating->stars ? '★' : '☆' }}
                                @endfor
                            </span>
                            <form method="POST" action="{{ route('student.ratings.destroy', $rating) }}" onsubmit="return confirm('حذف هذا التقييم؟')">
                                @csrf @method('DELETE')
                                <button class="text-red-400 hover:text-red-600 text-sm">حذف</button>
                            </form>
                        </div>
                    </div>
                    @if($rating->comment)
                        <p class="text-sm text-gray-500">{{ $rating->comment }}</p>
                    @endif
                </div>
                @endforeach
            @else
                <p class="text-gray-400 text-center py-8">لم تكتب أي تقييم بعد</p>
            @endif
        </div>
    </div>
</div>
@endsection
