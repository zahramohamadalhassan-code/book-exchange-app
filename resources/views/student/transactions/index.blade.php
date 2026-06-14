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
                            <div x-data="{ 
                                showRatingModal: false, 
                                stars: '5',
                                get autoComment() {
                                    if(this.stars == '5') return 'ممتاز';
                                    if(this.stars == '4') return 'جيد جداً';
                                    if(this.stars == '3') return 'جيد';
                                    if(this.stars == '2') return 'عادي';
                                    return 'سيء';
                                }
                            }">
                                <button @click="showRatingModal = true" class="bg-yellow-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-yellow-600 transition-all duration-200">
                                    <x-heroicon name="star" class="w-4 h-4 inline" /> {{ __('messages.student.transactions.rate') }}
                                </button>
                                
                                {{-- Rating Modal --}}
                                <div x-show="showRatingModal" 
                                     @keydown.escape.window="showRatingModal = false"
                                     class="fixed inset-0 z-50 overflow-y-auto" 
                                     style="display: none;">
                                    <div class="flex items-center justify-center min-h-screen px-4 py-6">
                                        {{-- Backdrop --}}
                                        <div x-show="showRatingModal"
                                             x-transition:enter="ease-out duration-300"
                                             x-transition:enter-start="opacity-0"
                                             x-transition:enter-end="opacity-100"
                                             x-transition:leave="ease-in duration-200"
                                             x-transition:leave-start="opacity-100"
                                             x-transition:leave-end="opacity-0"
                                             class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"
                                             @click="showRatingModal = false"></div>

                                        {{-- Panel --}}
                                        <div x-show="showRatingModal"
                                             x-transition:enter="ease-out duration-300"
                                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                             x-transition:leave="ease-in duration-200"
                                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                             class="relative bg-white rounded-2xl shadow-2xl w-full sm:max-w-md p-6 z-10 border border-gray-100"
                                             @click.stop>

                                            {{-- Header --}}
                                            <div class="flex items-center justify-between mb-5">
                                                <div class="flex items-center gap-3">
                                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-yellow-100 text-yellow-600 flex items-center justify-center">
                                                        <x-heroicon name="star" class="w-5 h-5" />
                                                    </div>
                                                    <h3 class="text-lg font-bold text-gray-900">{{ __('messages.student.transactions.rate_heading') }}</h3>
                                                </div>
                                                <button @click="showRatingModal = false" 
                                                        class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-1.5 transition-all duration-200 hover:rotate-90">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            <form method="POST" action="{{ route('student.ratings.store') }}">
                                                @csrf
                                                <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                                                
                                                <div class="mb-4">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.student.transactions.stars') }} <span class="text-red-500">*</span></label>
                                                    <select name="stars" x-model="stars" required class="w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                                                        <option value="5">⭐⭐⭐⭐⭐ {{ __('messages.student.transactions.excellent') }}</option>
                                                        <option value="4">⭐⭐⭐⭐ {{ __('messages.student.transactions.very_good') }}</option>
                                                        <option value="3">⭐⭐⭐ {{ __('messages.student.transactions.good') }}</option>
                                                        <option value="2">⭐⭐ {{ __('messages.student.transactions.acceptable') }}</option>
                                                        <option value="1">⭐ {{ __('messages.student.transactions.poor') }}</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="mb-5">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.student.transactions.comment') }}</label>
                                                    <input type="text" :value="autoComment" disabled class="w-full border border-gray-200 rounded-xl px-3 py-2 bg-gray-50 text-gray-600 font-medium">
                                                    <input type="hidden" name="comment" :value="autoComment">
                                                </div>
                                                
                                                <div class="flex gap-3">
                                                    <button type="submit" class="flex-1 bg-yellow-500 text-white py-2.5 rounded-xl hover:bg-yellow-600 font-medium transition-all duration-200 hover:shadow-lg hover:shadow-yellow-200">{{ __('messages.student.transactions.submit_rating') }}</button>
                                                    <button type="button" @click="showRatingModal = false" class="flex-1 bg-gray-100 text-gray-700 py-2.5 rounded-xl hover:bg-gray-200 font-medium transition-all duration-200">{{ __('messages.books.cancel') }}</button>
                                                </div>
                                            </form>
                                        </div>
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
