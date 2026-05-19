@extends('layouts.app')
@section('title', __('messages.student.notes.create.title') . ' - ' . __('messages.app_name'))

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 flex items-center gap-2"><x-heroicon name="document-text" class="w-8 h-8 text-purple-600" /> {{ __('messages.student.notes.create.title') }}</h1>

    <form method="POST" action="{{ route('student.notes.store') }}" enctype="multipart/form-data" x-data="noteForm()" class="bg-white rounded-xl shadow-sm border p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.student.notes.create.summary_title') }} *</label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="{{ __('messages.student.notes.create.summary_title_placeholder') }}">
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.student.notes.create.description') }}</label>
            <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="{{ __('messages.student.notes.create.description_placeholder') }}">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.student.notes.create.category') }} *</label>
            <select name="category_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <option value="">{{ __('messages.student.notes.create.choose_department') }}</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->faculty_name }} - {{ $cat->department_name }} ({{ $cat->study_year }})
                    </option>
                @endforeach
            </select>
            @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.student.notes.create.pdf_file') }} *</label>
            <input type="file" name="pdf_file" accept=".pdf" required @change="handlePdfUpload($event)"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 file:ms-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:bg-indigo-50 file:text-indigo-600 file:font-medium">
            <p class="text-xs text-gray-400 mt-1">{{ __('messages.student.notes.create.pdf_hint') }}</p>
            <template x-if="aiLoading">
                <p class="text-xs text-indigo-600 mt-1 flex items-center gap-1">
                    <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    @if(app()->getLocale() === 'ar')
                        جارٍ تحليل الملف بالذكاء الاصطناعي...
                    @else
                        Analyzing file with AI...
                    @endif
                </p>
            </template>
            <template x-if="aiSuccess">
                <p class="text-xs text-green-600 mt-1">
                    @if(app()->getLocale() === 'ar')
                        تم التحقق من الملف بنجاح
                    @else
                        File verified successfully
                    @endif
                </p>
            </template>
            <template x-if="aiError">
                <p class="text-xs text-red-500 mt-1" x-text="aiError"></p>
            </template>
            @error('pdf_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" :disabled="aiLoading" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-bold text-lg hover:bg-indigo-700 transition disabled:opacity-50">
            {{ __('messages.student.notes.create.upload_btn') }}
        </button>
    </form>
</div>
@push('scripts')
<script>
function noteForm() {
    return {
        aiLoading: false,
        aiSuccess: false,
        aiError: '',

        async handlePdfUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.aiLoading = true;
            this.aiSuccess = false;
            this.aiError = '';

            const formData = new FormData();
            formData.append('pdf_file', file);

            try {
                const response = await fetch('{{ route("ai.moderate-pdf") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    this.aiSuccess = true;
                } else {
                    event.target.value = '';
                    this.aiError = result.message || '{{ app()->getLocale() === "ar" ? "الملف غير مقبول" : "File not accepted" }}';
                }
            } catch (e) {
                event.target.value = '';
                this.aiError = '{{ app()->getLocale() === "ar" ? "حدث خطأ أثناء التحليل" : "An error occurred during analysis" }}';
            } finally {
                this.aiLoading = false;
            }
        }
    }
}
</script>
@endpush
@endsection
