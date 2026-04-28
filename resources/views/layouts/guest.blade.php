<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BookExchange')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">
    @include('components.navbar')

    <main class="flex-1 flex flex-col justify-center py-12 bg-gray-50 sm:px-6 lg:px-8">
        @if(session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
                 class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mx-auto mb-6 max-w-md text-center" x-transition>
                {{ session('success') }}
            </div>
        @endif

        @hasSection('content')
            @yield('content')
        @else
            <div class="w-full flex justify-center items-center mt-8 md:mt-12">
                <div class="w-full max-w-md mx-4">
                    <div class="bg-white py-10 px-8 shadow-2xl rounded-3xl border border-gray-100">
                        {{ $slot ?? '' }}
                    </div>
                </div>
            </div>
        @endif
    </main>

    @include('components.footer')
    @stack('scripts')
</body>
</html>
