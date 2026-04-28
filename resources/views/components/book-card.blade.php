{{-- بطاقة كتاب - مُعاد استخدامها --}}
@props(['book'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300 group">
    {{-- صورة الغلاف --}}
    <div class="relative h-48 bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center overflow-hidden">
        @if($book->cover_image_url)
            <img src="{{ asset('storage/' . $book->cover_image_url) }}" alt="{{ $book->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <span class="text-6xl opacity-50">📖</span>
        @endif

        {{-- شارة نوع العرض --}}
        <span class="absolute top-3 right-3 px-3 py-1 rounded-full text-xs font-bold text-white
            {{ $book->offer_type === 'sale' ? 'bg-green-500' : ($book->offer_type === 'exchange' ? 'bg-blue-500' : 'bg-orange-500') }}">
            {{ $book->offer_type === 'sale' ? 'للبيع' : ($book->offer_type === 'exchange' ? 'للتبادل' : 'تبرع') }}
        </span>
    </div>

    {{-- معلومات الكتاب --}}
    <div class="p-4">
        <h3 class="font-bold text-gray-800 text-lg mb-1 line-clamp-1">{{ $book->title }}</h3>

        @if($book->author)
            <p class="text-gray-500 text-sm mb-2">{{ $book->author }}</p>
        @endif

        <div class="flex items-center justify-between mt-3">
            {{-- السعر --}}
            @if($book->offer_type === 'sale' && $book->price)
                <span class="text-indigo-600 font-bold text-lg">${{ number_format($book->price, 2) }}</span>
            @elseif($book->offer_type === 'donate')
                <span class="text-orange-500 font-bold">مجاني 🎁</span>
            @else
                <span class="text-blue-500 font-bold">تبادل 🔄</span>
            @endif

            {{-- حالة الكتاب --}}
            <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600">
                {{ $book->condition === 'excellent' ? 'ممتاز' : ($book->condition === 'good' ? 'جيد' : ($book->condition === 'fair' ? 'مقبول' : 'ضعيف')) }}
            </span>
        </div>

        {{-- القسم --}}
        @if($book->category)
            <p class="text-xs text-gray-400 mt-2">{{ $book->category->department_name }}</p>
        @endif

        {{-- زر التفاصيل --}}
        <a href="{{ route('books.show', $book) }}"
           class="mt-3 block text-center bg-indigo-50 text-indigo-600 py-2 rounded-lg hover:bg-indigo-100 transition font-medium text-sm">
            عرض التفاصيل
        </a>
    </div>
</div>
