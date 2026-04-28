@extends('layouts.app')
@section('title', $book->title . ' - منصة تبادل الكتب')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- صورة الكتاب --}}
        <div class="md:col-span-1">
            <div class="bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl h-80 flex items-center justify-center overflow-hidden">
                @if($book->cover_image_url)
                    <img src="{{ asset('storage/' . $book->cover_image_url) }}" alt="{{ $book->title }}" class="w-full h-full object-cover rounded-2xl">
                @else
                    <span class="text-8xl opacity-50">📖</span>
                @endif
            </div>
        </div>

        {{-- تفاصيل الكتاب --}}
        <div class="md:col-span-2">
            <div class="flex items-start justify-between mb-4">
                <h1 class="text-3xl font-bold text-gray-800">{{ $book->title }}</h1>
                <span class="px-4 py-2 rounded-full text-sm font-bold text-white
                    {{ $book->offer_type === 'sale' ? 'bg-green-500' : ($book->offer_type === 'exchange' ? 'bg-blue-500' : 'bg-orange-500') }}">
                    {{ $book->offer_type === 'sale' ? 'للبيع' : ($book->offer_type === 'exchange' ? 'للتبادل' : 'تبرع') }}
                </span>
            </div>

            @if($book->author)
                <p class="text-gray-500 text-lg mb-4">المؤلف: {{ $book->author }}</p>
            @endif

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">الحالة</p>
                    <p class="font-bold text-gray-800">
                        {{ $book->condition === 'excellent' ? 'ممتاز' : ($book->condition === 'good' ? 'جيد' : ($book->condition === 'fair' ? 'مقبول' : 'ضعيف')) }}
                    </p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">السعر</p>
                    <p class="font-bold text-indigo-600 text-xl">
                        @if($book->offer_type === 'sale' && $book->price)
                            ${{ number_format($book->price, 2) }}
                        @elseif($book->offer_type === 'donate')
                            مجاني 🎁
                        @else
                            تبادل 🔄
                        @endif
                    </p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">القسم</p>
                    <p class="font-bold text-gray-800">{{ $book->category?->department_name }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">صاحب الكتاب</p>
                    <p class="font-bold text-gray-800">{{ $book->user?->full_name }}</p>
                </div>
            </div>

            {{-- زر طلب الكتاب --}}
            @auth
                @if($book->user_id !== auth()->id() && $book->status === 'available')
                <div x-data="{ showModal: false }">
                    <button @click="showModal = true"
                            class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold text-lg hover:bg-indigo-700 transition w-full md:w-auto">
                        📩 طلب هذا الكتاب
                    </button>

                    {{-- نافذة طلب الكتاب --}}
                    <div x-show="showModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showModal = false">
                        <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl">
                            <h3 class="text-xl font-bold mb-4">طلب كتاب: {{ $book->title }}</h3>
                            <form method="POST" action="{{ route('student.transactions.store') }}">
                                @csrf
                                <input type="hidden" name="book_id" value="{{ $book->id }}">

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ الاستلام (اختياري)</label>
                                    <input type="date" name="meeting_date" class="w-full border rounded-lg px-3 py-2">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">وقت الاستلام (اختياري)</label>
                                    <input type="time" name="meeting_time" class="w-full border rounded-lg px-3 py-2">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">مكان الاستلام (اختياري)</label>
                                    <input type="text" name="meeting_location" placeholder="مثال: بوابة الجامعة" class="w-full border rounded-lg px-3 py-2">
                                </div>

                                <div class="flex gap-3">
                                    <button type="submit" class="flex-1 bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 font-medium">إرسال الطلب</button>
                                    <button type="button" @click="showModal = false" class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 font-medium">إلغاء</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @elseif($book->user_id === auth()->id())
                    <p class="text-gray-400 font-medium">هذا كتابك ✨</p>
                @endif
            @else
                <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold text-lg hover:bg-indigo-700 transition inline-block">
                    سجّل دخولك لطلب الكتاب
                </a>
            @endauth
        </div>
    </div>

    {{-- كتب مشابهة --}}
    @if($relatedBooks->count() > 0)
    <section class="mt-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">📖 كتب مشابهة</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($relatedBooks as $related)
                @include('components.book-card', ['book' => $related])
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection
