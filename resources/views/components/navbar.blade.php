{{-- شريط التنقل العلوي --}}
<nav x-data="{ open: false }" class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            {{-- الشعار --}}
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="text-2xl">📚</span>
                    <span class="text-xl font-bold text-indigo-600">تبادل الكتب</span>
                </a>
            </div>

            {{-- روابط التنقل (Desktop) --}}
            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-indigo-600 transition font-medium {{ request()->routeIs('home') ? 'text-indigo-600' : '' }}">الرئيسية</a>
                <a href="{{ route('books.browse') }}" class="text-gray-700 hover:text-indigo-600 transition font-medium {{ request()->routeIs('books.*') ? 'text-indigo-600' : '' }}">الكتب</a>
                <a href="{{ route('notes.browse') }}" class="text-gray-700 hover:text-indigo-600 transition font-medium {{ request()->routeIs('notes.*') ? 'text-indigo-600' : '' }}">الملخصات</a>

                @auth
                    <a href="{{ route('student.dashboard') }}" class="text-gray-700 hover:text-indigo-600 transition font-medium">لوحتي</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-red-500 hover:text-red-700 transition font-medium">خروج</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">تسجيل الدخول</a>
                    <a href="{{ route('register') }}" class="border border-indigo-600 text-indigo-600 px-4 py-2 rounded-lg hover:bg-indigo-50 transition font-medium">حساب جديد</a>
                @endauth
            </div>

            {{-- زر القائمة (Mobile) --}}
            <div class="md:hidden flex items-center">
                <button @click="open = !open" class="text-gray-600 hover:text-gray-900 p-2">
                    <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- قائمة الموبايل --}}
    <div x-show="open" x-transition class="md:hidden border-t">
        <div class="px-4 py-3 space-y-2">
            <a href="{{ route('home') }}" class="block py-2 text-gray-700 hover:text-indigo-600">الرئيسية</a>
            <a href="{{ route('books.browse') }}" class="block py-2 text-gray-700 hover:text-indigo-600">الكتب</a>
            <a href="{{ route('notes.browse') }}" class="block py-2 text-gray-700 hover:text-indigo-600">الملخصات</a>
            @auth
                <a href="{{ route('student.dashboard') }}" class="block py-2 text-gray-700 hover:text-indigo-600">لوحتي</a>
            @else
                <a href="{{ route('login') }}" class="block py-2 text-indigo-600 font-medium">تسجيل الدخول</a>
            @endauth
        </div>
    </div>
</nav>
