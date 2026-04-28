@extends('layouts.app')
@section('title', 'تصفح الكتب - منصة تبادل الكتب')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">📚 تصفح الكتب المتاحة</h1>

    {{-- فلاتر البحث --}}
    <div x-data="{ showFilters: false }" class="mb-8">
        <div class="flex flex-wrap gap-4 items-center">
            <form method="GET" action="{{ route('books.browse') }}" class="flex flex-wrap gap-3 items-center flex-1">
                {{-- بحث --}}
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن كتاب..."
                       class="border border-gray-300 rounded-lg px-4 py-2 flex-1 min-w-[200px] focus:ring-2 focus:ring-indigo-500 focus:border-transparent">

                {{-- نوع العرض --}}
                <select name="offer_type" class="border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">كل الأنواع</option>
                    <option value="sale" {{ request('offer_type') == 'sale' ? 'selected' : '' }}>للبيع</option>
                    <option value="exchange" {{ request('offer_type') == 'exchange' ? 'selected' : '' }}>للتبادل</option>
                    <option value="donate" {{ request('offer_type') == 'donate' ? 'selected' : '' }}>تبرع</option>
                </select>

                {{-- الحالة --}}
                <select name="condition" class="border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">كل الحالات</option>
                    <option value="excellent" {{ request('condition') == 'excellent' ? 'selected' : '' }}>ممتاز</option>
                    <option value="good" {{ request('condition') == 'good' ? 'selected' : '' }}>جيد</option>
                    <option value="fair" {{ request('condition') == 'fair' ? 'selected' : '' }}>مقبول</option>
                    <option value="poor" {{ request('condition') == 'poor' ? 'selected' : '' }}>ضعيف</option>
                </select>

                {{-- القسم --}}
                <select name="category_id" class="border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">كل الأقسام</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->department_name }} - {{ $cat->study_year }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">بحث</button>

                @if(request()->hasAny(['search', 'offer_type', 'condition', 'category_id']))
                    <a href="{{ route('books.browse') }}" class="text-red-500 hover:text-red-700 text-sm">مسح الفلاتر</a>
                @endif
            </form>
        </div>
    </div>

    {{-- شبكة الكتب --}}
    @if($books->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($books as $book)
            @include('components.book-card', ['book' => $book])
        @endforeach
    </div>

    {{-- ترقيم الصفحات --}}
    <div class="mt-8">
        {{ $books->appends(request()->query())->links() }}
    </div>
    @else
    <div class="text-center py-16 text-gray-400">
        <span class="text-6xl block mb-4">🔍</span>
        <p class="text-xl">لم يتم العثور على كتب تطابق بحثك</p>
        <a href="{{ route('books.browse') }}" class="text-indigo-600 hover:underline mt-2 inline-block">عرض جميع الكتب</a>
    </div>
    @endif
</div>
@endsection
