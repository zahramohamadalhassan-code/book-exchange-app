@extends('layouts.app')

@section('title', 'منصة تبادل الكتب الجامعية - الصفحة الرئيسية')

@section('content')
{{-- قسم البطل (Hero Section) --}}
<section class="bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4 leading-tight">
            📚 تبادل الكتب بين طلاب الجامعة
        </h1>
        <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">
            بِع، تبادل، أو تبرع بكتبك الجامعية. شارك ملخصاتك الرقمية وساعد زملاءك.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('books.browse') }}" class="bg-white text-indigo-600 px-8 py-3 rounded-xl font-bold text-lg hover:bg-indigo-50 transition shadow-lg">
                تصفح الكتب
            </a>
            @guest
            <a href="{{ route('register') }}" class="border-2 border-white text-white px-8 py-3 rounded-xl font-bold text-lg hover:bg-white/10 transition">
                انضم الآن
            </a>
            @endguest
        </div>
    </div>
</section>

{{-- إحصائيات --}}
<section class="py-10 -mt-10">
    <div class="max-w-5xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-xl p-6 grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
            <div>
                <div class="text-3xl font-extrabold text-indigo-600">{{ $stats['books_count'] }}</div>
                <div class="text-gray-500 mt-1">كتاب متاح</div>
            </div>
            <div>
                <div class="text-3xl font-extrabold text-purple-600">{{ $stats['notes_count'] }}</div>
                <div class="text-gray-500 mt-1">ملخص رقمي</div>
            </div>
            <div>
                <div class="text-3xl font-extrabold text-green-600">{{ $stats['users_count'] }}</div>
                <div class="text-gray-500 mt-1">طالب مسجل</div>
            </div>
        </div>
    </div>
</section>

{{-- أحدث الكتب --}}
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-gray-800">📖 أحدث الكتب المتاحة</h2>
            <a href="{{ route('books.browse') }}" class="text-indigo-600 hover:underline font-medium">عرض الكل ←</a>
        </div>

        @if($latestBooks->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($latestBooks as $book)
                @include('components.book-card', ['book' => $book])
            @endforeach
        </div>
        @else
        <div class="text-center py-12 text-gray-400">
            <span class="text-5xl block mb-3">📭</span>
            <p>لا توجد كتب متاحة حالياً</p>
        </div>
        @endif
    </div>
</section>

{{-- أحدث الملخصات --}}
<section class="py-12 bg-gray-100">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-gray-800">📝 أحدث الملخصات الرقمية</h2>
            <a href="{{ route('notes.browse') }}" class="text-indigo-600 hover:underline font-medium">عرض الكل ←</a>
        </div>

        @if($latestNotes->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($latestNotes as $note)
            <div class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-3xl">📄</span>
                    <div>
                        <h3 class="font-bold text-gray-800 line-clamp-1">{{ $note->title }}</h3>
                        <p class="text-xs text-gray-400">{{ $note->category?->department_name }}</p>
                    </div>
                </div>
                @if($note->description)
                    <p class="text-sm text-gray-500 line-clamp-2 mb-3">{{ $note->description }}</p>
                @endif
                <a href="{{ route('notes.show', $note) }}" class="text-indigo-600 text-sm font-medium hover:underline">عرض الملخص ←</a>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12 text-gray-400">
            <span class="text-5xl block mb-3">📭</span>
            <p>لا توجد ملخصات متاحة حالياً</p>
        </div>
        @endif
    </div>
</section>

{{-- دعوة للعمل --}}
<section class="py-16 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-center">
    <div class="max-w-3xl mx-auto px-4">
        <h2 class="text-3xl font-bold mb-4">هل لديك كتب لا تحتاجها؟</h2>
        <p class="text-lg text-purple-100 mb-6">ساعد زملاءك بمشاركة كتبك أو ملخصاتك. سجّل الآن وابدأ!</p>
        @guest
        <a href="{{ route('register') }}" class="bg-white text-indigo-600 px-8 py-3 rounded-xl font-bold text-lg hover:bg-indigo-50 transition inline-block">
            إنشاء حساب مجاني
        </a>
        @else
        <a href="{{ route('student.books.create') }}" class="bg-white text-indigo-600 px-8 py-3 rounded-xl font-bold text-lg hover:bg-indigo-50 transition inline-block">
            أضف كتابك الآن
        </a>
        @endguest
    </div>
</section>
@endsection
