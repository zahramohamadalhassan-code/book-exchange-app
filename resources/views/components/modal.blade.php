@props(['id' => 'modal', 'title' => '', 'maxWidth' => 'md', 'icon' => '', 'iconColor' => 'indigo'])

@php
$maxWidthClass = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth] ?? 'sm:max-w-md';

$iconColorClasses = [
    'indigo' => 'bg-indigo-100 text-indigo-600',
    'red' => 'bg-red-100 text-red-600',
    'yellow' => 'bg-yellow-100 text-yellow-600',
    'green' => 'bg-green-100 text-green-600',
    'blue' => 'bg-blue-100 text-blue-600',
    'purple' => 'bg-purple-100 text-purple-600',
][$iconColor] ?? 'bg-indigo-100 text-indigo-600';
@endphp

<div x-data="{ show: false }" 
     id="{{ $id }}" 
     @open-modal.window="$event.detail.id === '{{ $id }}' && (show = true)" 
     @close-modal.window="show = false"
     @keydown.escape.window="show = false"
     x-show="show" 
     class="fixed inset-0 z-50 overflow-y-auto" 
     style="display: none;"
     aria-labelledby="modal-{{ $id }}-title" 
     role="dialog" 
     aria-modal="true">
    
    {{-- Backdrop with blur --}}
    <div class="flex items-center justify-center min-h-screen px-4 py-6">
        <div x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"
             @click="show = false"
             aria-hidden="true"></div>

        {{-- Modal Panel --}}
        <div x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative bg-white rounded-2xl shadow-2xl w-full {{ $maxWidthClass }} p-6 z-10 border border-gray-100"
             @click.stop>
            
            {{-- Header --}}
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    @if($icon)
                    <div class="flex-shrink-0 w-10 h-10 rounded-xl {{ $iconColorClasses }} flex items-center justify-center">
                        <x-heroicon :name="$icon" class="w-5 h-5" />
                    </div>
                    @endif
                    <h3 class="text-lg font-bold text-gray-900" id="modal-{{ $id }}-title">{{ $title }}</h3>
                </div>
                <button @click="show = false" 
                        class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-1.5 transition-all duration-200 hover:rotate-90">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            {{ $slot }}
        </div>
    </div>
</div>
