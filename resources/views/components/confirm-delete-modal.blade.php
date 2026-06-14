{{-- 
    Reusable Delete Confirmation Modal
    Usage: Include this once per page, then trigger via Alpine.js:
    x-data="{ deleteModalOpen: false, deleteUrl: '', itemTitle: '' }"
    @click="deleteModalOpen = true; deleteUrl = '...'; itemTitle = '...'"
--}}

<div x-show="deleteModalOpen" 
     @keydown.escape.window="deleteModalOpen = false"
     class="fixed inset-0 z-50 overflow-y-auto" 
     style="display: none;"
     aria-labelledby="delete-modal-title" 
     role="dialog" 
     aria-modal="true">
    
    <div class="flex items-center justify-center min-h-screen px-4 py-6">
        {{-- Backdrop --}}
        <div x-show="deleteModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"
             @click="deleteModalOpen = false"
             aria-hidden="true"></div>

        {{-- Modal Panel --}}
        <div x-show="deleteModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative bg-white rounded-2xl shadow-2xl w-full sm:max-w-md p-6 z-10 border border-gray-100"
             @click.stop>
            
            {{-- Icon --}}
            <div class="flex justify-center mb-4">
                <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center animate-pulse">
                    <svg class="w-7 h-7 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                </div>
            </div>

            {{-- Title --}}
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2" id="delete-modal-title">
                {{ app()->getLocale() === 'ar' ? 'تأكيد الحذف' : 'Confirm Deletion' }}
            </h3>

            {{-- Description --}}
            <p class="text-sm text-gray-500 text-center mb-6 leading-relaxed">
                {{ app()->getLocale() === 'ar' ? 'هل أنت متأكد أنك تريد حذف' : 'Are you sure you want to delete' }}
                <span class="font-bold text-gray-800" x-text="itemTitle"></span>{{ app()->getLocale() === 'ar' ? '؟' : '?' }}
                <br>
                <span class="text-xs text-red-400 mt-1 inline-block">
                    {{ app()->getLocale() === 'ar' ? 'لا يمكن التراجع عن هذا الإجراء.' : 'This action cannot be undone.' }}
                </span>
            </p>

            {{-- Actions --}}
            <div class="flex gap-3">
                <button type="button" @click="deleteModalOpen = false"
                        class="flex-1 bg-gray-100 text-gray-700 py-2.5 rounded-xl hover:bg-gray-200 font-medium transition-all duration-200 text-sm">
                    {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </button>
                <form method="POST" :action="deleteUrl" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full bg-red-600 text-white py-2.5 rounded-xl hover:bg-red-700 font-medium transition-all duration-200 text-sm inline-flex items-center justify-center gap-2 hover:shadow-lg hover:shadow-red-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                        {{ app()->getLocale() === 'ar' ? 'حذف' : 'Delete' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
