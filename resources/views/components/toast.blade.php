{{-- رسائل التنبيه المؤقتة --}}
@if(session('success') || session('error') || session('warning'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform -translate-y-4"
     class="fixed top-20 left-1/2 -translate-x-1/2 z-50 min-w-[320px]">

    @if(session('success'))
    <div class="bg-green-50 border border-green-300 text-green-800 rounded-lg px-5 py-3 shadow-lg flex items-center gap-3">
        <span class="text-xl">✅</span>
        <p class="font-medium">{{ session('success') }}</p>
        <button @click="show = false" class="mr-auto text-green-500 hover:text-green-700">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-300 text-red-800 rounded-lg px-5 py-3 shadow-lg flex items-center gap-3">
        <span class="text-xl">❌</span>
        <p class="font-medium">{{ session('error') }}</p>
        <button @click="show = false" class="mr-auto text-red-500 hover:text-red-700">&times;</button>
    </div>
    @endif

    @if(session('warning'))
    <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-lg px-5 py-3 shadow-lg flex items-center gap-3">
        <span class="text-xl">⚠️</span>
        <p class="font-medium">{{ session('warning') }}</p>
        <button @click="show = false" class="mr-auto text-yellow-500 hover:text-yellow-700">&times;</button>
    </div>
    @endif
</div>
@endif
