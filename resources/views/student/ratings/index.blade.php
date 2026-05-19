@extends('layouts.app')
@section('title', __('messages.student.ratings.title') . ' - ' . __('messages.app_name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 flex items-center gap-2"><x-heroicon name="star" class="w-8 h-8 text-yellow-500" /> {{ __('messages.student.ratings.title') }}</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
            <h2 class="text-xl font-bold text-gray-700 mb-4 flex items-center gap-2"><x-heroicon name="arrow-down-tray" class="w-5 h-5 text-indigo-500" /> {{ __('messages.student.ratings.received') }}</h2>
            @if($receivedRatings->count() > 0)
                @foreach($receivedRatings as $rating)
                <div class="bg-white rounded-xl shadow-sm border p-4 mb-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-medium text-gray-800">{{ $rating->reviewer?->full_name }}</p>
                        <div class="text-yellow-500">
                            @for($i = 1; $i <= 5; $i++)
                                {{ $i <= $rating->stars ? '&#9733;' : '&#9734;' }}
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
                <p class="text-gray-400 text-center py-8">{{ __('messages.student.ratings.no_received') }}</p>
            @endif
        </div>

        <div>
            <h2 class="text-xl font-bold text-gray-700 mb-4 flex items-center gap-2"><x-heroicon name="arrow-up-tray" class="w-5 h-5 text-green-500" /> {{ __('messages.student.ratings.given') }}</h2>
            @if($givenRatings->count() > 0)
                @foreach($givenRatings as $rating)
                <div class="bg-white rounded-xl shadow-sm border p-4 mb-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-medium text-gray-800">{{ __('messages.student.ratings.to') }} {{ $rating->reviewedUser?->full_name }}</p>
                        <div class="flex items-center gap-2">
                            <span class="text-yellow-500">
                                @for($i = 1; $i <= 5; $i++)
                                    {{ $i <= $rating->stars ? '&#9733;' : '&#9734;' }}
                                @endfor
                            </span>
                            <form method="POST" action="{{ route('student.ratings.destroy', $rating) }}" onsubmit="return confirm('{{ __('messages.student.ratings.delete_rating') }}')">
                                @csrf @method('DELETE')
                                <button class="text-red-400 hover:text-red-600 text-sm flex items-center gap-1"><x-heroicon name="trash" class="w-4 h-4" /></button>
                            </form>
                        </div>
                    </div>
                    @if($rating->comment)
                        <p class="text-sm text-gray-500">{{ $rating->comment }}</p>
                    @endif
                </div>
                @endforeach
            @else
                <p class="text-gray-400 text-center py-8">{{ __('messages.student.ratings.no_given') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
