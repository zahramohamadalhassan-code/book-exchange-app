@extends('layouts.app')
@section('title', __('messages.student.transactions.title') . ' - ' . __('messages.app_name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 flex items-center gap-2"><x-heroicon name="arrow-path" class="w-8 h-8 text-green-600" /> {{ __('messages.student.transactions.title') }}</h1>

    <div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
        <form method="GET" action="{{ route('student.transactions.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/3">
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.student.transactions.text_search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.student.transactions.search_placeholder') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div class="w-full md:w-1/4">
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.student.transactions.request_type') }}</label>
                <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">{{ __('messages.student.transactions.all') }}</option>
                    <option value="incoming" {{ request('type') === 'incoming' ? 'selected' : '' }}>{{ __('messages.student.transactions.incoming') }}</option>
                    <option value="outgoing" {{ request('type') === 'outgoing' ? 'selected' : '' }}>{{ __('messages.student.transactions.outgoing') }}</option>
                </select>
            </div>

            <div class="w-full md:w-1/4">
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.student.transactions.request_status') }}</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">{{ __('messages.student.transactions.all_statuses') }}</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('messages.student.transactions.pending') }}</option>
                    <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>{{ __('messages.student.transactions.accepted') }}</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('messages.student.transactions.completed') }}</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('messages.student.transactions.cancelled') }}</option>
                </select>
            </div>

            <div class="w-full md:w-auto">
                <button type="submit" class="w-full md:w-auto bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">{{ __('messages.student.transactions.filter') }}</button>
            </div>
        </form>
    </div>

    @if($transactions->count() > 0)
    <div class="space-y-4">
        @foreach($transactions as $transaction)
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <span class="text-indigo-600"><x-heroicon name="book-open" class="w-8 h-8" /></span>
                    <div>
                        <h3 class="font-bold text-gray-800">{{ $transaction->book?->title ?? __('messages.student.transactions.deleted_book') }}</h3>
                        <p class="text-sm text-gray-500">
                            @if($transaction->owner_id === auth()->id())
                                <span class="text-blue-600">{{ __('messages.student.transactions.incoming_request') }}</span> {{ __('messages.student.transactions.from') }}: {{ $transaction->requester?->full_name }}
                            @else
                                <span class="text-green-600">{{ __('messages.student.transactions.outgoing_request') }}</span> {{ __('messages.student.transactions.to') }}: {{ $transaction->owner?->full_name }}
                            @endif
                        </p>
                        @if($transaction->book && $transaction->book->offer_type === 'exchange')
                            <div class="mt-2 bg-purple-50 border border-purple-100 rounded p-2 text-sm text-purple-700">
                                <x-heroicon name="arrow-path" class="w-4 h-4 inline" /> <strong>{{ __('messages.student.transactions.required_for_swap') }}</strong> {{ $transaction->book->exchange_for ?? __('messages.student.transactions.undefined') }}
                            </div>
                        @endif

                        @if($transaction->meeting_date)
                            <p class="text-xs text-gray-400 mt-2 flex items-center gap-1.5"><x-heroicon name="calendar" class="w-3.5 h-3.5" /> {{ $transaction->meeting_date->format('Y-m-d') }}
                                @if($transaction->meeting_time) @if(app()->getLocale() === 'ar') في @else at @endif {{ $transaction->meeting_time }} @endif
                                @if($transaction->meeting_location) | <x-heroicon name="map-pin" class="w-3.5 h-3.5" /> {{ $transaction->meeting_location }} @endif
                            </p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full text-xs font-bold
                        {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-700' :
                           ($transaction->status === 'accepted' ? 'bg-blue-100 text-blue-700' :
                           ($transaction->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')) }}">
                        {{ $transaction->status === 'pending' ? __('messages.student.transactions.pending') :
                           ($transaction->status === 'accepted' ? __('messages.student.transactions.accepted') :
                           ($transaction->status === 'completed' ? __('messages.student.transactions.completed') : __('messages.student.transactions.cancelled'))) }}
                    </span>

                    @if($transaction->owner_id === auth()->id() && $transaction->status === 'pending')
                    <form method="POST" action="{{ route('student.transactions.update', $transaction) }}" class="flex gap-2">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="accepted">
                        <button class="bg-green-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-green-600">{{ __('messages.student.transactions.accept') }}</button>
                    </form>
                    <form method="POST" action="{{ route('student.transactions.update', $transaction) }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="cancelled">
                        <button class="bg-red-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-red-600">{{ __('messages.student.transactions.reject') }}</button>
                    </form>
                    @endif

                    @if($transaction->status === 'accepted' && ($transaction->owner_id === auth()->id() || $transaction->requester_id === auth()->id()))
                    <form method="POST" action="{{ route('student.transactions.update', $transaction) }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="completed">
                        <button class="bg-indigo-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-indigo-600">{{ __('messages.student.transactions.confirm_receipt') }}</button>
                    </form>
                    @endif

                    @if($transaction->status === 'completed')
                        @php
                            $hasRated = \App\Models\Rating::where('transaction_id', $transaction->id)->where('reviewer_id', auth()->id())->exists();
                        @endphp
                        
                        @if(!$hasRated)
                            <div x-data="{ showRatingModal: false }">
                                <button @click="showRatingModal = true" class="bg-yellow-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-yellow-600">
                                    <x-heroicon name="star" class="w-4 h-4 inline" /> {{ __('messages.student.transactions.rate') }}
                                </button>
                                
                                <div x-show="showRatingModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showRatingModal = false">
                                    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl">
                                        <h3 class="text-xl font-bold mb-4">{{ __('messages.student.transactions.rate_heading') }}</h3>
                                        <form method="POST" action="{{ route('student.ratings.store') }}">
                                            @csrf
                                            <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                                            
                                            <div class="mb-4">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.student.transactions.stars') }} <span class="text-red-500">*</span></label>
                                                <select name="stars" required class="w-full border rounded-lg px-3 py-2">
                                                    <option value="5">⭐⭐⭐⭐⭐ {{ __('messages.student.transactions.excellent') }}</option>
                                                    <option value="4">⭐⭐⭐⭐ {{ __('messages.student.transactions.very_good') }}</option>
                                                    <option value="3">⭐⭐⭐ {{ __('messages.student.transactions.good') }}</option>
                                                    <option value="2">⭐⭐ {{ __('messages.student.transactions.acceptable') }}</option>
                                                    <option value="1">⭐ {{ __('messages.student.transactions.poor') }}</option>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-4">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.student.transactions.comment') }}</label>
                                                <select name="comment" class="w-full border rounded-lg px-3 py-2">
                                                    <option value="">{{ __('messages.student.transactions.no_comment') }}</option>
                                                    <option value="ممتاز">{{ __('messages.student.transactions.excellent') }}</option>
                                                    <option value="جيد جداً">{{ __('messages.student.transactions.very_good') }}</option>
                                                    <option value="جيد">{{ __('messages.student.transactions.good') }}</option>
                                                    <option value="عادي">{{ __('messages.student.transactions.acceptable') }}</option>
                                                    <option value="سيء">{{ __('messages.student.transactions.poor') }}</option>
                                                </select>
                                            </div>
                                            
                                            <div class="flex gap-3">
                                                <button type="submit" class="flex-1 bg-yellow-500 text-white py-2 rounded-lg hover:bg-yellow-600 font-medium">{{ __('messages.student.transactions.submit_rating') }}</button>
                                                <button type="button" @click="showRatingModal = false" class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 font-medium">{{ __('messages.books.cancel') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <span class="text-xs text-gray-500 font-medium bg-gray-100 px-2 py-1 rounded border">{{ __('messages.student.transactions.already_rated') }}</span>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="mt-6">
        {{ $transactions->appends(request()->query())->links() }}
    </div>
    @else
        <div class="text-center py-16 bg-white rounded-xl border">
            <div class="flex justify-center mb-3"><x-heroicon name="inbox" class="w-16 h-16 text-gray-300" /></div>
        <p class="text-gray-500">{{ __('messages.student.transactions.no_transactions') }}</p>
    </div>
    @endif
</div>
@endsection
