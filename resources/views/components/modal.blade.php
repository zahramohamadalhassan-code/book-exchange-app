@props(['id' => 'modal', 'title' => ''])

<div x-data="{ show: false }" id="{{ $id }}" @open-modal.window="$event.detail.id === '{{ $id }}' && (show = true)" @close-modal.window="show = false"
     x-show="show" class="fixed inset-0 z-50 overflow-y-auto" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div @click="show = false" class="fixed inset-0 bg-gray-900 bg-opacity-50"></div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                <button @click="show = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            {{ $slot }}
        </div>
    </div>
</div>
