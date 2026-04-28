@extends('layouts.app')
@section('title', 'إضافة كتاب - منصة تبادل الكتب')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">📖 إضافة كتاب جديد</h1>

    <form method="POST" action="{{ route('student.books.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border p-6 space-y-5">
        @csrf

        {{-- صورة الغلاف مع AI Auto-fill --}}
        <div x-data="bookAutoFill()">
            <label class="block text-sm font-medium text-gray-700 mb-1">صورة الغلاف</label>
            <input type="file" name="cover_image" accept="image/*" @change="handleImageUpload($event)"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 file:ml-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:bg-indigo-50 file:text-indigo-600 file:font-medium">
            <p class="text-xs text-gray-400 mt-1">ارفع صورة الغلاف وسيقوم الذكاء الاصطناعي بملء بيانات الكتاب تلقائياً</p>
            <div x-show="loading" class="text-indigo-600 text-sm mt-2">⏳ جاري تحليل الصورة بالذكاء الاصطناعي...</div>
        </div>

        {{-- العنوان --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">عنوان الكتاب *</label>
            <input type="text" name="title" id="book-title" value="{{ old('title') }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500" placeholder="أدخل عنوان الكتاب">
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- المؤلف --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">المؤلف</label>
            <input type="text" name="author" id="book-author" value="{{ old('author') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="اسم المؤلف (اختياري)">
        </div>

        {{-- القسم --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">القسم / التصنيف *</label>
            <select name="category_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <option value="">اختر القسم</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->faculty_name }} - {{ $cat->department_name }} ({{ $cat->study_year }})
                    </option>
                @endforeach
            </select>
            @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- حالة الكتاب --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">حالة الكتاب *</label>
            <select name="condition" id="book-condition" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <option value="excellent" {{ old('condition') == 'excellent' ? 'selected' : '' }}>ممتاز</option>
                <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>جيد</option>
                <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>مقبول</option>
                <option value="poor" {{ old('condition') == 'poor' ? 'selected' : '' }}>ضعيف</option>
            </select>
        </div>

        {{-- نوع العرض --}}
        <div x-data="{ offerType: '{{ old('offer_type', 'sale') }}' }">
            <label class="block text-sm font-medium text-gray-700 mb-1">نوع العرض *</label>
            <select name="offer_type" x-model="offerType" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <option value="sale">بيع</option>
                <option value="exchange">تبادل</option>
                <option value="donate">تبرع</option>
            </select>

            {{-- السعر (يظهر فقط عند البيع) --}}
            <div x-show="offerType === 'sale'" x-transition class="mt-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">السعر ($)</label>
                <div class="flex items-center gap-2">
                    <input type="number" name="price" id="book-price" value="{{ old('price') }}" step="0.01" min="0"
                           class="flex-1 border border-gray-300 rounded-lg px-3 py-2" placeholder="أدخل السعر">
                    <button type="button" onclick="suggestPrice()" class="bg-purple-100 text-purple-600 px-3 py-2 rounded-lg text-sm font-medium hover:bg-purple-200 transition">
                        🤖 اقتراح سعر
                    </button>
                </div>
                <p id="ai-price-hint" class="text-xs text-purple-500 mt-1 hidden"></p>
            </div>
        </div>

        <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-bold text-lg hover:bg-indigo-700 transition">
            إضافة الكتاب
        </button>
    </form>
</div>

@push('scripts')
<script>
function bookAutoFill() {
    return {
        loading: false,
        async handleImageUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.loading = true;
            const formData = new FormData();
            formData.append('cover_image', file);

            try {
                const response = await fetch('{{ route("ai.extract-book") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                });
                const data = await response.json();
                if (data.success && data.data) {
                    if (data.data.title) document.getElementById('book-title').value = data.data.title;
                    if (data.data.author) document.getElementById('book-author').value = data.data.author;
                }
            } catch (e) {
                console.error('AI extract error:', e);
            } finally {
                this.loading = false;
            }
        }
    }
}

async function suggestPrice() {
    const title = document.getElementById('book-title').value;
    const condition = document.getElementById('book-condition').value;
    if (!title) { alert('أدخل عنوان الكتاب أولاً'); return; }

    const hint = document.getElementById('ai-price-hint');
    hint.textContent = '⏳ جاري تقدير السعر...';
    hint.classList.remove('hidden');

    try {
        const response = await fetch('{{ route("ai.predict-price") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ title, condition })
        });
        const data = await response.json();
        if (data.success) {
            document.getElementById('book-price').value = data.price;
            hint.textContent = `🤖 السعر المقترح: $${data.price}`;
        }
    } catch (e) {
        hint.textContent = '❌ تعذر تقدير السعر';
    }
}
</script>
@endpush
@endsection
