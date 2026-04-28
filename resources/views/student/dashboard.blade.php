@extends('layouts.app')
@section('title', 'لوحة التحكم - منصة تبادل الكتب')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">مرحباً، {{ auth()->user()->full_name }} 👋</h1>
    <p class="text-gray-500 mb-8">إليك نظرة عامة على نشاطك في المنصة</p>

    {{-- إحصائيات --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border p-6 flex items-center gap-4">
            <div class="bg-indigo-100 text-indigo-600 p-3 rounded-xl text-2xl">📚</div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $booksCount }}</p>
                <p class="text-gray-500 text-sm">كتبي المنشورة</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-6 flex items-center gap-4">
            <div class="bg-purple-100 text-purple-600 p-3 rounded-xl text-2xl">📝</div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $notesCount }}</p>
                <p class="text-gray-500 text-sm">ملخصاتي</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-6 flex items-center gap-4">
            <div class="bg-orange-100 text-orange-600 p-3 rounded-xl text-2xl">⏳</div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $pendingRequests }}</p>
                <p class="text-gray-500 text-sm">طلبات بانتظار ردي</p>
            </div>
        </div>
    </div>

    {{-- روابط سريعة --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('student.books.create') }}" class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition text-center group">
            <span class="text-3xl block mb-2 group-hover:scale-110 transition-transform">📖</span>
            <p class="font-bold text-gray-800">إضافة كتاب</p>
            <p class="text-sm text-gray-500">أضف كتاباً للبيع أو التبادل</p>
        </a>
        <a href="{{ route('student.notes.create') }}" class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition text-center group">
            <span class="text-3xl block mb-2 group-hover:scale-110 transition-transform">📄</span>
            <p class="font-bold text-gray-800">رفع ملخص</p>
            <p class="text-sm text-gray-500">شارك ملخصاتك مع زملائك</p>
        </a>
        <a href="{{ route('student.transactions.index') }}" class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition text-center group">
            <span class="text-3xl block mb-2 group-hover:scale-110 transition-transform">🔄</span>
            <p class="font-bold text-gray-800">عملياتي</p>
            <p class="text-sm text-gray-500">تتبع طلبات التبادل</p>
        </a>
        <a href="{{ route('student.favorites.index') }}" class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition text-center group">
            <span class="text-3xl block mb-2 group-hover:scale-110 transition-transform">❤️</span>
            <p class="font-bold text-gray-800">المفضلة</p>
            <p class="text-sm text-gray-500">الكتب والملخصات المحفوظة</p>
        </a>
    </div>
</div>
@endsection
