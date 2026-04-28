@extends('layouts.app')
@section('title', 'عملياتي - منصة تبادل الكتب')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">🔄 عمليات التبادل</h1>

    @if($transactions->count() > 0)
    <div class="space-y-4">
        @foreach($transactions as $transaction)
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <span class="text-3xl">📖</span>
                    <div>
                        <h3 class="font-bold text-gray-800">{{ $transaction->book?->title ?? 'كتاب محذوف' }}</h3>
                        <p class="text-sm text-gray-500">
                            @if($transaction->owner_id === auth()->id())
                                <span class="text-blue-600">طلب وارد</span> من: {{ $transaction->requester?->full_name }}
                            @else
                                <span class="text-green-600">طلب صادر</span> إلى: {{ $transaction->owner?->full_name }}
                            @endif
                        </p>
                        @if($transaction->meeting_date)
                            <p class="text-xs text-gray-400 mt-1">📅 {{ $transaction->meeting_date->format('Y-m-d') }}
                                @if($transaction->meeting_time) في {{ $transaction->meeting_time }} @endif
                                @if($transaction->meeting_location) | 📍 {{ $transaction->meeting_location }} @endif
                            </p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    {{-- شارة الحالة --}}
                    <span class="px-3 py-1 rounded-full text-xs font-bold
                        {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-700' :
                           ($transaction->status === 'accepted' ? 'bg-blue-100 text-blue-700' :
                           ($transaction->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')) }}">
                        {{ $transaction->status === 'pending' ? 'قيد الانتظار' :
                           ($transaction->status === 'accepted' ? 'مقبول' :
                           ($transaction->status === 'completed' ? 'مكتمل' : 'ملغي')) }}
                    </span>

                    {{-- أزرار الإجراءات (فقط للمالك عند pending أو accepted) --}}
                    @if($transaction->owner_id === auth()->id() && $transaction->status === 'pending')
                    <form method="POST" action="{{ route('student.transactions.update', $transaction) }}" class="flex gap-2">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="accepted">
                        <button class="bg-green-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-green-600">قبول</button>
                    </form>
                    <form method="POST" action="{{ route('student.transactions.update', $transaction) }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="cancelled">
                        <button class="bg-red-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-red-600">رفض</button>
                    </form>
                    @endif

                    @if($transaction->status === 'accepted' && ($transaction->owner_id === auth()->id() || $transaction->requester_id === auth()->id()))
                    <form method="POST" action="{{ route('student.transactions.update', $transaction) }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="completed">
                        <button class="bg-indigo-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-indigo-600">تأكيد الاستلام</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-xl border">
        <span class="text-5xl block mb-3">📭</span>
        <p class="text-gray-500">لا توجد عمليات تبادل حالياً</p>
    </div>
    @endif
</div>
@endsection
