@extends('layouts.app')
@section('title', __('messages.student.books.create.title') . ' - ' . __('messages.app_name'))

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('student.books.index') }}" class="text-gray-500 hover:text-indigo-600">
            <x-heroicon name="{{ app()->getLocale() === 'ar' ? 'arrow-right' : 'arrow-left' }}" class="w-6 h-6" />
        </a>
        <h1 class="text-3xl font-bold text-gray-800">{{ __('messages.student.books.create.title') }}</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border p-8">
        <form method="POST" action="{{ route('student.books.store') }}" enctype="multipart/form-data" x-data="bookForm()">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="md:col-span-2 bg-indigo-50 border border-indigo-100 rounded-xl p-6 mb-2">
                    <div class="flex items-start gap-4">
                        <div class="bg-indigo-100 p-3 rounded-full text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09l2.846.813-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-indigo-900 mb-1">
                                @if(app()->getLocale() === 'ar')
                                    الرفع الذكي بالكاميرا
                                @else
                                    Smart Upload via Camera
                                @endif
                            </h3>
                            <p class="text-sm text-indigo-700 mb-4">
                                @if(app()->getLocale() === 'ar')
                                    قم برفع غلاف الكتاب أولاً. سيقوم الذكاء الاصطناعي بتحليل الصورة، تقييم حالة الكتاب، وملء الحقول المتبقية تلقائياً لتوفير وقتك!
                                @else
                                    Upload the book cover first. Our AI will analyze the image, evaluate the book's condition, and auto-fill the remaining fields to save your time!
                                @endif
                            </p>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.student.books.create.cover_image') }}</label>
                            <input type="file" name="cover_image" accept="image/*" @change="handleCoverUpload($event)" class="w-full border border-white bg-white rounded-xl px-4 py-3 file:me-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 shadow-sm">
                            <p class="text-xs text-gray-500 mt-2">{{ __('messages.student.books.create.supported_formats') }}</p>
                            <template x-if="aiLoading">
                                <p class="text-sm text-indigo-600 mt-2 flex items-center gap-2 font-medium">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    @if(app()->getLocale() === 'ar')
                                        جارٍ تحليل غلاف الكتاب وتقييم حالته بالذكاء الاصطناعي...
                                    @else
                                        Analyzing book cover and evaluating condition with AI...
                                    @endif
                                </p>
                            </template>
                            <template x-if="aiSuccess">
                                <p class="text-sm text-green-600 mt-2 font-medium flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                    @if(app()->getLocale() === 'ar')
                                        تم تقييم حالة الكتاب وملء الحقول تلقائياً بنجاح!
                                    @else
                                        Book condition evaluated and fields auto-filled successfully!
                                    @endif
                                </p>
                            </template>
                            <template x-if="aiError">
                                <p class="text-sm text-orange-500 mt-2 font-medium flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                    <span x-text="aiError"></span>
                                </p>
                            </template>
                            @error('cover_image') <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.student.books.create.book_title') }} <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" name="title" x-ref="titleInput" value="{{ old('title') }}" required class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" :class="aiLoading && 'bg-indigo-50 animate-pulse'">
                        <template x-if="aiLoading">
                            <span class="absolute end-3 top-1/2 -translate-y-1/2">
                                <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </span>
                        </template>
                    </div>
                    @error('title') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.student.books.create.author_name') }}</label>
                    <div class="relative">
                        <input type="text" name="author" x-ref="authorInput" value="{{ old('author') }}" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" :class="aiLoading && 'bg-indigo-50 animate-pulse'">
                        <template x-if="aiLoading">
                            <span class="absolute end-3 top-1/2 -translate-y-1/2">
                                <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </span>
                        </template>
                    </div>
                    @error('author') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.student.books.create.department') }} <span class="text-red-500">*</span></label>
                    <select name="category_id" required class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">{{ __('messages.student.books.create.choose_department') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->faculty_name }} - {{ $category->department_name }} ({{ $category->study_year }})
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.student.books.create.book_condition') }} <span class="text-red-500">*</span></label>
                    <select name="condition" x-ref="conditionInput" required class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">{{ __('messages.student.books.create.choose_condition') }}</option>
                        <option value="excellent" {{ old('condition') == 'excellent' ? 'selected' : '' }}>{{ __('messages.student.books.create.excellent_new') }}</option>
                        <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>{{ __('messages.student.books.create.good_used') }}</option>
                        <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>{{ __('messages.student.books.create.fair_marks') }}</option>
                        <option value="poor" {{ old('condition') == 'poor' ? 'selected' : '' }}>{{ __('messages.student.books.create.poor_worn') }}</option>
                    </select>
                    @error('condition') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.student.books.create.pages_count') }}</label>
                    <input type="number" name="pages_count" x-ref="pagesInput" value="{{ old('pages_count') }}" min="1" placeholder="{{ __('messages.student.books.create.pages_count_placeholder') }}" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('pages_count') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.student.books.create.offer_type') }} <span class="text-red-500">*</span></label>
                    <select name="offer_type" x-model="offerType" required class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">{{ __('messages.student.books.create.choose_offer') }}</option>
                        <option value="sale">{{ __('messages.student.books.create.sale') }}</option>
                        <option value="exchange">{{ __('messages.student.books.create.exchange') }}</option>
                        <option value="donate">{{ __('messages.student.books.create.donation') }}</option>
                    </select>
                    @error('offer_type') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div x-show="offerType === 'sale'" x-transition class="md:col-span-2">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-700">{{ __('messages.student.books.create.price_syp') }} <span class="text-red-500">*</span></label>
                        <button type="button" @click="suggestPrice()" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1" :disabled="aiPriceLoading">
                            <template x-if="aiPriceLoading">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </template>
                            <template x-if="!aiPriceLoading">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09l2.846.813-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" /></svg>
                            </template>
                            <span x-text="aiPriceLoading ? '...' : '{{ __('messages.student.books.create.suggest_price_btn') }}'"></span>
                        </button>
                    </div>
                    <input type="number" name="price" x-ref="priceInput" value="{{ old('price') }}" step="0.01" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" :class="aiPriceLoading && 'bg-indigo-50 animate-pulse'">
                    <template x-if="aiPriceSuccess">
                        <p class="text-xs text-green-600 mt-1">{{ __('messages.student.books.create.price_suggested') }}</p>
                    </template>
                    <template x-if="aiPriceError">
                        <p class="text-xs text-orange-500 mt-1" x-text="aiPriceError"></p>
                    </template>
                    @error('price') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div x-show="offerType === 'exchange'" x-transition class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.student.books.create.exchange_for') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="exchange_for" value="{{ old('exchange_for') }}" placeholder="{{ __('messages.student.books.create.exchange_for_placeholder') }}" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('exchange_for') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>


            </div>

            <div class="flex justify-end pt-6 border-t">
                <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold text-lg hover:bg-indigo-700 transition">{{ __('messages.student.books.create.add_book_btn') }}</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
function bookForm() {
    return {
        offerType: '{{ old("offer_type", "") }}',
        aiLoading: false,
        aiSuccess: false,
        aiError: '',
        
        aiPriceLoading: false,
        aiPriceSuccess: false,
        aiPriceError: '',

        async handleCoverUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.aiLoading = true;
            this.aiSuccess = false;
            this.aiError = '';

            const formData = new FormData();
            formData.append('cover_image', file);

            try {
                const response = await fetch('{{ route("ai.extract-book") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const result = await response.json();

                if (response.ok && result.success && result.data) {
                    const titleInput = this.$refs.titleInput;
                    const authorInput = this.$refs.authorInput;
                    const conditionInput = this.$refs.conditionInput;

                    if (result.data.title && !titleInput.value) {
                        titleInput.value = result.data.title;
                        titleInput.dispatchEvent(new Event('input'));
                    }
                    if (result.data.author && !authorInput.value) {
                        authorInput.value = result.data.author;
                        authorInput.dispatchEvent(new Event('input'));
                    }
                    if (result.data.condition && !conditionInput.value) {
                        conditionInput.value = result.data.condition;
                        conditionInput.dispatchEvent(new Event('change'));
                    }
                    this.aiSuccess = true;
                } else {
                    event.target.value = '';
                    this.aiError = result.message || '{{ app()->getLocale() === "ar" ? "لم يتم التعرف على بيانات الكتاب" : "Could not recognize book details" }}';
                }
            } catch (e) {
                event.target.value = '';
                this.aiError = '{{ app()->getLocale() === "ar" ? "حدث خطأ أثناء التحليل" : "An error occurred during analysis" }}';
            } finally {
                this.aiLoading = false;
            }
        },

        async suggestPrice() {
            const title = this.$refs.titleInput.value;
            const condition = this.$refs.conditionInput.value;
            const author = this.$refs.authorInput.value;
            const pagesCount = this.$refs.pagesInput.value;

            if (!title || !condition) {
                this.aiPriceError = '{{ app()->getLocale() === "ar" ? "يرجى إدخال العنوان واختيار الحالة أولاً" : "Please enter title and condition first" }}';
                setTimeout(() => this.aiPriceError = '', 3000);
                return;
            }

            this.aiPriceLoading = true;
            this.aiPriceSuccess = false;
            this.aiPriceError = '';

            try {
                const response = await fetch('{{ route("ai.predict-price") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ title, condition, author, pages_count: pagesCount || 0 }),
                });

                const result = await response.json();

                if (result.success && result.price) {
                    this.$refs.priceInput.value = result.price;
                    this.$refs.priceInput.dispatchEvent(new Event('input'));
                    this.aiPriceSuccess = true;
                } else {
                    this.aiPriceError = '{{ __('messages.student.books.create.price_suggest_error') }}';
                }
            } catch (e) {
                this.aiPriceError = '{{ __('messages.student.books.create.price_suggest_error') }}';
            } finally {
                this.aiPriceLoading = false;
                setTimeout(() => {
                    this.aiPriceSuccess = false;
                    this.aiPriceError = '';
                }, 4000);
            }
        }
    }
}
</script>
@endpush
@endsection
