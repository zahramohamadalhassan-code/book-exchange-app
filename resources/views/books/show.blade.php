@extends('layouts.app')
@section('title', $book->title . ' - ' . __('messages.app_name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-1">
            <div class="bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl h-80 flex items-center justify-center overflow-hidden">
                @if($book->cover_image_url)
                    <img src="{{ asset('storage/' . $book->cover_image_url) }}" alt="{{ $book->title }}" class="w-full h-full object-cover rounded-2xl">
                @else
                    <div class="flex justify-center"><x-heroicon name="book-open" class="w-20 h-20 text-indigo-300" /></div>
                @endif
            </div>
        </div>

        <div class="md:col-span-2">
            <div class="flex items-start justify-between mb-4">
                <h1 class="text-3xl font-bold text-gray-800">{{ $book->title }}</h1>
                <span class="px-4 py-2 rounded-full text-sm font-bold text-white
                    {{ $book->offer_type === 'sale' ? 'bg-green-500' : ($book->offer_type === 'exchange' ? 'bg-blue-500' : 'bg-orange-500') }}">
                    {{ $book->offer_type === 'sale' ? __('messages.offer_types.sale') : ($book->offer_type === 'exchange' ? __('messages.offer_types.exchange') : __('messages.offer_types.donate')) }}
                </span>
            </div>

            @if($book->author)
                <p class="text-gray-500 text-lg mb-4">{{ __('messages.books.author') }}: {{ $book->author }}</p>
            @endif

            @if($book->content_description)
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-4">
                    <p class="text-sm font-semibold text-blue-800 mb-1">
                        @if(app()->getLocale() === 'ar')
                            وصف محتوى الكتاب
                        @else
                            Book Content Description
                        @endif
                    </p>
                    <p class="text-sm text-blue-700 leading-relaxed">{{ $book->content_description }}</p>
                </div>
            @endif

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">{{ __('messages.books.condition') }}</p>
                    <p class="font-bold text-gray-800">
                        {{ $book->condition === 'excellent' ? __('messages.conditions.excellent') : ($book->condition === 'good' ? __('messages.conditions.good') : ($book->condition === 'fair' ? __('messages.conditions.fair') : __('messages.conditions.poor'))) }}
                    </p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">{{ __('messages.books.price') }}</p>
                    <p class="font-bold text-indigo-600 text-xl">
                        @if($book->offer_type === 'sale' && $book->price)
                            {{ number_format($book->price) }} SYP
                            @if($book->payment_method)
                                <span class="text-xs font-normal text-gray-500 block mt-1">
                                    @if($book->payment_method === 'cash_on_delivery')
                                        {{ app()->getLocale() === 'ar' ? 'الدفع نقداً عند الاستلام' : 'Cash on Delivery' }}
                                    @elseif($book->payment_method === 'syriatel_cash')
                                        {{ app()->getLocale() === 'ar' ? 'سيريتل كاش' : 'Syriatel Cash' }}
                                    @elseif($book->payment_method === 'mtn_cash')
                                        {{ app()->getLocale() === 'ar' ? 'كاش MTN' : 'MTN Cash' }}
                                    @elseif($book->payment_method === 'bank_transfer')
                                        {{ app()->getLocale() === 'ar' ? 'تحويل بنكي / شركة حوالات' : 'Bank Transfer / Exchange Company' }}
                                    @elseif($book->payment_method === 'cham_cash')
                                        {{ app()->getLocale() === 'ar' ? 'شام كاش' : 'Cham Cash' }}
                                    @endif
                                </span>
                            @endif
                        @elseif($book->offer_type === 'donate')
                            {{ __('messages.books.free') }} <x-heroicon name="gift" class="w-4 h-4 inline" />
                        @else
                            {{ __('messages.books.exchange') }} <x-heroicon name="arrow-path" class="w-4 h-4 inline" />
                            @if($book->exchange_for)
                                <div class="text-sm text-gray-500 font-normal mt-1">{{ __('messages.books.requested') }}: {{ $book->exchange_for }}</div>
                            @endif
                        @endif
                    </p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">{{ __('messages.books.department') }}</p>
                    <p class="font-bold text-gray-800">{{ $book->category?->faculty_name }} - {{ $book->category?->study_year }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">{{ __('messages.books.book_owner') }}</p>
                    <div class="flex items-center justify-between">
                        <p class="font-bold text-gray-800">{{ $book->user?->full_name }}</p>
                        <a href="{{ route('users.ratings', $book->user) }}" class="flex items-center text-yellow-500 hover:text-yellow-600 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ app()->getLocale() === 'ar' ? 'me-1' : 'ms-1' }}" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="font-bold text-base hover:underline">{{ number_format($book->user?->average_rating ?? 0, 1) }}</span>
                        </a>
                    </div>
                </div>
            </div>

            @auth
                @if($book->user_id !== auth()->id() && $book->status === 'available')
                <div x-data="{ showModal: false }">
                    <button @click="showModal = true"
                            class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold text-lg hover:bg-indigo-700 transition w-full md:w-auto inline-flex items-center gap-2">
                        <x-heroicon name="paper-airplane" class="w-5 h-5" /> {{ __('messages.books.request_book') }}
                    </button>

                    {{-- Book Request Modal --}}
                    <div x-show="showModal" 
                         @keydown.escape.window="showModal = false"
                         class="fixed inset-0 z-50 overflow-y-auto" 
                         style="display: none;">
                        <div class="flex items-center justify-center min-h-screen px-4 py-6">
                            {{-- Backdrop --}}
                            <div x-show="showModal"
                                 x-transition:enter="ease-out duration-300"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="ease-in duration-200"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"
                                 @click="showModal = false"></div>

                            {{-- Panel --}}
                            <div x-show="showModal"
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
                                        <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                            <x-heroicon name="paper-airplane" class="w-5 h-5" />
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900">{{ __('messages.books.request_heading') }} {{ $book->title }}</h3>
                                    </div>
                                    <button @click="showModal = false" 
                                            class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-1.5 transition-all duration-200 hover:rotate-90">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                <form method="POST" action="{{ route('student.transactions.store') }}">
                                    @csrf
                                    <input type="hidden" name="book_id" value="{{ $book->id }}">

                                    @if($book->offer_type === 'exchange')
                                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                                            <p class="text-sm text-blue-800">
                                                <strong>{{ __('messages.books.required_for_exchange') }}</strong> {{ $book->exchange_for ?? __('messages.student.transactions.undefined') }}
                                            </p>
                                            <p class="text-xs text-blue-600 mt-1">{{ __('messages.books.exchange_note') }}</p>
                                        </div>
                                    @elseif($book->offer_type === 'sale')
                                        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl">
                                            <p class="text-sm text-green-800">
                                                <strong>{{ app()->getLocale() === 'ar' ? 'طريقة الدفع:' : 'Payment Method:' }}</strong> 
                                                @if($book->payment_method === 'cash_on_delivery')
                                                    {{ app()->getLocale() === 'ar' ? 'الدفع نقداً عند الاستلام' : 'Cash on Delivery' }}
                                                @elseif($book->payment_method === 'syriatel_cash')
                                                    {{ app()->getLocale() === 'ar' ? 'سيريتل كاش' : 'Syriatel Cash' }}
                                                @elseif($book->payment_method === 'mtn_cash')
                                                    {{ app()->getLocale() === 'ar' ? 'كاش MTN' : 'MTN Cash' }}
                                                @elseif($book->payment_method === 'bank_transfer')
                                                    {{ app()->getLocale() === 'ar' ? 'تحويل بنكي / شركة حوالات' : 'Bank Transfer / Exchange Company' }}
                                                @elseif($book->payment_method === 'cham_cash')
                                                    {{ app()->getLocale() === 'ar' ? 'شام كاش' : 'Cham Cash' }}
                                                @else
                                                    {{ app()->getLocale() === 'ar' ? 'غير محدد' : 'Not specified' }}
                                                @endif
                                            </p>
                                        </div>
                                    @endif

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.books.meeting_date') }}</label>
                                        <input type="date" name="meeting_date" class="w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.books.meeting_time') }}</label>
                                        <input type="time" name="meeting_time" class="w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                    </div>
                                    <div class="mb-5">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.books.meeting_location') }}</label>
                                        <input type="text" name="meeting_location" placeholder="{{ __('messages.books.meeting_location_placeholder') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                    </div>

                                    <div class="flex gap-3">
                                        <button type="submit" class="flex-1 bg-indigo-600 text-white py-2.5 rounded-xl hover:bg-indigo-700 font-medium transition-all duration-200 hover:shadow-lg hover:shadow-indigo-200">{{ __('messages.books.send_request') }}</button>
                                        <button type="button" @click="showModal = false" class="flex-1 bg-gray-100 text-gray-700 py-2.5 rounded-xl hover:bg-gray-200 font-medium transition-all duration-200">{{ __('messages.books.cancel') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @elseif($book->user_id === auth()->id())
                    <p class="text-gray-400 font-medium flex items-center gap-1"><x-heroicon name="check-circle" class="w-5 h-5" /> {{ __('messages.books.your_book') }}</p>
                @endif
            @else
                <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold text-lg hover:bg-indigo-700 transition inline-block">
                    {{ __('messages.books.login_to_request') }}
                </a>
            @endauth
        </div>
    </div>

    @if($relatedBooks->count() > 0)
    <section class="mt-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2"><x-heroicon name="book-open" class="w-7 h-7 text-indigo-600" /> {{ __('messages.books.similar_books') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($relatedBooks as $related)
                @include('components.book-card', ['book' => $related])
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection
