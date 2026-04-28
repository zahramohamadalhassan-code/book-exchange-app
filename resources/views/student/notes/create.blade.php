@extends('layouts.app')
@section('title', 'رفع ملخص - منصة تبادل الكتب')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">📄 رفع ملخص جديد</h1>

    <form method="POST" action="{{ route('student.notes.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">عنوان الملخص *</label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="مثال: ملخص الفيزياء - الفصل الأول">
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
            <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="وصف مختصر للملخص (اختياري)">{{ old('description') }}</textarea>
        </div>

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

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">ملف PDF *</label>
            <input type="file" name="pdf_file" accept=".pdf" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 file:ml-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:bg-indigo-50 file:text-indigo-600 file:font-medium">
            <p class="text-xs text-gray-400 mt-1">ملف PDF فقط، الحجم الأقصى 10 ميجابايت</p>
            @error('pdf_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-bold text-lg hover:bg-indigo-700 transition">
            رفع الملخص
        </button>
    </form>
</div>
@endsection
