@extends('layouts.app')
@section('title', 'ملخصاتي - منصة تبادل الكتب')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">📝 ملخصاتي</h1>
        <a href="{{ route('student.notes.create') }}" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">+ رفع ملخص</a>
    </div>

    @if($notes->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($notes as $note)
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold text-gray-800">{{ $note->title }}</h3>
                <span class="text-xs px-2 py-1 rounded-full {{ $note->moderation_status === 'approved' ? 'bg-green-100 text-green-700' : ($note->moderation_status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                    {{ $note->moderation_status === 'approved' ? 'معتمد' : ($note->moderation_status === 'rejected' ? 'مرفوض' : 'قيد المراجعة') }}
                </span>
            </div>
            <p class="text-sm text-gray-500 line-clamp-2 mb-3">{{ $note->description ?? 'بدون وصف' }}</p>
            <p class="text-xs text-gray-400 mb-3">{{ $note->category?->department_name }}</p>
            <div class="flex gap-2">
                @if($note->pdf_file_url)
                <a href="{{ asset('storage/' . $note->pdf_file_url) }}" target="_blank" class="text-indigo-600 text-sm hover:underline">📥 تحميل</a>
                @endif
                <form method="POST" action="{{ route('student.notes.destroy', $note) }}" onsubmit="return confirm('هل أنت متأكد؟')" class="mr-auto">
                    @csrf @method('DELETE')
                    <button class="text-red-500 text-sm hover:underline">حذف</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $notes->links() }}</div>
    @else
    <div class="text-center py-16 bg-white rounded-xl border">
        <span class="text-5xl block mb-3">📭</span>
        <p class="text-gray-500 mb-4">لم تقم برفع أي ملخص بعد</p>
        <a href="{{ route('student.notes.create') }}" class="bg-indigo-600 text-white px-5 py-2 rounded-lg">ارفع ملخصك الأول</a>
    </div>
    @endif
</div>
@endsection
