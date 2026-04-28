@extends('layouts.app')
@section('title', 'تعديل كتاب - منصة تبادل الكتب')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">✏️ تعديل الكتاب</h1>

    <form method="POST" action="{{ route('student.books.update', $book) }}" class="bg-white rounded-xl shadow-sm border p-6 space-y-5">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">عنوان الكتاب *</label>
            <input type="text" name="title" value="{{ old('title', $book->title) }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">حالة الكتاب *</label>
            <select name="condition" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <option value="excellent" {{ $book->condition == 'excellent' ? 'selected' : '' }}>ممتاز</option>
                <option value="good" {{ $book->condition == 'good' ? 'selected' : '' }}>جيد</option>
                <option value="fair" {{ $book->condition == 'fair' ? 'selected' : '' }}>مقبول</option>
                <option value="poor" {{ $book->condition == 'poor' ? 'selected' : '' }}>ضعيف</option>
            </select>
        </div>

        <div x-data="{ offerType: '{{ old('offer_type', $book->offer_type) }}' }">
            <label class="block text-sm font-medium text-gray-700 mb-1">نوع العرض *</label>
            <select name="offer_type" x-model="offerType" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <option value="sale">بيع</option>
                <option value="exchange">تبادل</option>
                <option value="donate">تبرع</option>
            </select>

            <div x-show="offerType === 'sale'" class="mt-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">السعر ($)</label>
                <input type="number" name="price" value="{{ old('price', $book->price) }}" step="0.01" min="0"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">حالة الإتاحة *</label>
            <select name="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <option value="available" {{ $book->status == 'available' ? 'selected' : '' }}>متاح</option>
                <option value="pending" {{ $book->status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                <option value="sold" {{ $book->status == 'sold' ? 'selected' : '' }}>مباع/مسلّم</option>
            </select>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="flex-1 bg-indigo-600 text-white py-3 rounded-xl font-bold hover:bg-indigo-700 transition">حفظ التعديلات</button>
            <a href="{{ route('student.books.index') }}" class="flex-1 text-center bg-gray-200 text-gray-700 py-3 rounded-xl font-bold hover:bg-gray-300 transition">إلغاء</a>
        </div>
    </form>
</div>
@endsection
