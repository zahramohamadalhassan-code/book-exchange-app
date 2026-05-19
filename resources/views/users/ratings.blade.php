@extends('layouts.app')
@section('title', __('messages.student.ratings.title') . ' ' . $user->full_name . ' - ' . __('messages.app_name'))

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-sm border p-8 mb-8 text-center">
        <div class="w-24 h-24 bg-indigo-100 text-indigo-500 rounded-full flex items-center justify-center text-4xl mx-auto mb-4 font-bold">
            {{ mb_substr($user->full_name, 0, 1) }}
        </div>
        <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $user->full_name }}</h1>
        <div class="flex items-center justify-center text-yellow-500 text-xl font-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }}" viewBox="0 0 20 20" fill="currentColor">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
            <span>{{ number_format($user->average_rating, 1) }} / 5.0</span>
            <span class="text-gray-400 text-sm {{ app()->getLocale() === 'ar' ? 'me-2' : 'ms-2' }} font-normal">({{ $ratings->total() }} {{ __('messages.users.ratings_count') }})</span>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">{{ __('messages.users.comments_and_ratings') }}</h2>

        @if($ratings->count() > 0)
            <div class="space-y-6">
                @foreach($ratings as $rating)
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-100 text-indigo-500 rounded-full flex items-center justify-center font-bold">
                                    {{ mb_substr($rating->reviewer->full_name ?? __('messages.users.unknown'), 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ $rating->reviewer->full_name ?? __('messages.users.deleted_user') }}</p>
                                    <p class="text-xs text-gray-500">{{ $rating->created_at->format('Y-m-d') }}</p>
                                </div>
                            </div>
                            <div class="flex text-yellow-500">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $i <= $rating->stars ? 'text-yellow-500' : 'text-gray-300' }}" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                        </div>
                        @if($rating->comment)
                            <p class="text-gray-700 leading-relaxed mt-2 text-sm bg-white p-4 rounded-lg border border-gray-100">
                                "{{ $rating->comment }}"
                            </p>
                        @else
                            <p class="text-gray-400 italic text-sm mt-2">{{ __('messages.users.no_comment') }}</p>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $ratings->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <span class="text-5xl block mb-4">🌟</span>
                <p class="text-gray-500 text-lg">{{ __('messages.users.no_ratings') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
