@extends('layouts.app')
@section('title', 'كتبي - منصة تبادل الكتب')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">📚 كتبي</h1>
        <a href="{{ route('student.books.create') }}" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">+ إضافة كتاب</a>
    </div>

    @if($books->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <table class="w-full text-right">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-sm font-medium text-gray-500">العنوان</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-500">النوع</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-500">الحالة</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-500">الموافقة</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-500">السعر</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-500">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($books as $book)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $book->title }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-1 rounded-full {{ $book->offer_type === 'sale' ? 'bg-green-100 text-green-700' : ($book->offer_type === 'exchange' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700') }}">
                            {{ $book->offer_type === 'sale' ? 'بيع' : ($book->offer_type === 'exchange' ? 'تبادل' : 'تبرع') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm">{{ $book->status }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-1 rounded-full {{ $book->moderation_status === 'approved' ? 'bg-green-100 text-green-700' : ($book->moderation_status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ $book->moderation_status === 'approved' ? 'معتمد' : ($book->moderation_status === 'rejected' ? 'مرفوض' : 'قيد المراجعة') }}
                        </span>
                    </td>
                    <td class="px-4 py-3">${{ $book->price ?? '-' }}</td>
                    <td class="px-4 py-3 flex gap-2">
                        <a href="{{ route('student.books.edit', $book) }}" class="text-indigo-600 hover:underline text-sm">تعديل</a>
                        <form method="POST" action="{{ route('student.books.destroy', $book) }}" onsubmit="return confirm('هل أنت متأكد؟')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:underline text-sm">حذف</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $books->links() }}</div>
    @else
    <div class="text-center py-16 bg-white rounded-xl border">
        <span class="text-5xl block mb-3">📭</span>
        <p class="text-gray-500 mb-4">لم تقم بإضافة أي كتاب بعد</p>
        <a href="{{ route('student.books.create') }}" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700">أضف كتابك الأول</a>
    </div>
    @endif
</div>
@endsection
