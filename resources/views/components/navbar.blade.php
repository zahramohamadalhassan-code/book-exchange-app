<nav x-data="{ open: false }" class="bg-white shadow-sm border-t-4 border-yellow-500 border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <x-logo size="sm" :text="false" />
                    <span class="text-2xl font-extrabold text-indigo-800">{{ __('messages.app_name') }}</span>
                </a>
            </div>

            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-gray-800 hover:text-indigo-600 transition font-bold text-[15px] whitespace-nowrap {{ request()->routeIs('home') ? 'text-indigo-600' : '' }}">
                    <x-heroicon name="home" class="w-5 h-5" />
                    {{ __('messages.nav.home') }}
                </a>
                <a href="{{ route('books.browse') }}" class="flex items-center gap-2 text-gray-800 hover:text-indigo-600 transition font-bold text-[15px] whitespace-nowrap {{ request()->routeIs('books.*') ? 'text-indigo-600' : '' }}">
                    <x-heroicon name="book-open" class="w-5 h-5" />
                    {{ __('messages.nav.books') }}
                </a>
                <a href="{{ route('notes.browse') }}" class="flex items-center gap-2 text-gray-800 hover:text-indigo-600 transition font-bold text-[15px] whitespace-nowrap {{ request()->routeIs('notes.*') ? 'text-indigo-600' : '' }}">
                    <x-heroicon name="document-text" class="w-5 h-5" />
                    {{ __('messages.nav.notes') }}
                </a>

                @auth
                    <a href="{{ route('student.dashboard') }}" class="flex items-center gap-2 text-gray-800 hover:text-indigo-600 transition font-bold text-[15px] whitespace-nowrap {{ request()->routeIs('student.dashboard') ? 'text-indigo-600' : '' }}">
                        <x-heroicon name="squares-2x2" class="w-5 h-5" />
                        {{ __('messages.nav.my_dashboard') }}
                    </a>
                    <a href="{{ route('student.books.index') }}" class="flex items-center gap-2 text-gray-800 hover:text-indigo-600 transition font-bold text-[15px] whitespace-nowrap {{ request()->routeIs('student.books.*') ? 'text-indigo-600' : '' }}">
                        <x-heroicon name="book-open" class="w-5 h-5" />
                        {{ __('messages.nav.my_books') }}
                    </a>
                    <a href="{{ route('student.notes.index') }}" class="flex items-center gap-2 text-gray-800 hover:text-indigo-600 transition font-bold text-[15px] whitespace-nowrap {{ request()->routeIs('student.notes.*') ? 'text-indigo-600' : '' }}">
                        <x-heroicon name="document-text" class="w-5 h-5" />
                        {{ __('messages.nav.my_notes') }}
                    </a>
                    <a href="{{ route('student.transactions.index') }}" class="flex items-center gap-2 text-gray-800 hover:text-indigo-600 transition font-bold text-[15px] whitespace-nowrap {{ request()->routeIs('student.transactions.*') ? 'text-indigo-600' : '' }}">
                        <x-heroicon name="arrows-right-left" class="w-5 h-5" />
                        {{ app()->getLocale() === 'ar' ? 'التبادل والطلبات' : 'Exchanges & Requests' }}
                    </a>
                    <a href="{{ route('student.profile.edit') }}" class="flex items-center gap-2 text-gray-800 hover:text-indigo-600 transition font-bold text-[15px] whitespace-nowrap {{ request()->routeIs('student.profile.*') ? 'text-indigo-600' : '' }}">
                        <x-heroicon name="cog" class="w-5 h-5" />
                        {{ __('messages.nav.my_account') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 text-red-600 hover:text-red-800 transition font-bold text-[15px] whitespace-nowrap">
                            <x-heroicon name="arrow-left-on-rectangle" class="w-5 h-5" />
                            {{ __('messages.nav.logout') }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="bg-indigo-700 text-white px-5 py-2.5 rounded-lg hover:bg-indigo-800 transition font-bold text-[15px] whitespace-nowrap">{{ __('messages.nav.login') }}</a>
                    <a href="{{ route('register') }}" class="border-2 border-yellow-500 text-indigo-800 bg-yellow-50 px-5 py-2.5 rounded-lg hover:bg-yellow-100 transition font-bold text-[15px] whitespace-nowrap">{{ __('messages.nav.register') }}</a>
                @endauth

                <div class="flex items-center border-2 border-gray-300 rounded-lg overflow-hidden ms-2">
                    <a href="{{ route('locale.switch', 'ar') }}" class="px-3 py-1.5 text-sm font-bold {{ app()->getLocale() === 'ar' ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100' }} transition">عربي</a>
                    <a href="{{ route('locale.switch', 'en') }}" class="px-3 py-1.5 text-sm font-bold {{ app()->getLocale() === 'en' ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100' }} transition">EN</a>
                </div>
            </div>

            <div class="md:hidden flex items-center gap-2">
                <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                    <a href="{{ route('locale.switch', 'ar') }}" class="px-2 py-1 text-xs font-bold {{ app()->getLocale() === 'ar' ? 'bg-indigo-600 text-white' : 'text-gray-600' }}">عربي</a>
                    <a href="{{ route('locale.switch', 'en') }}" class="px-2 py-1 text-xs font-bold {{ app()->getLocale() === 'en' ? 'bg-indigo-600 text-white' : 'text-gray-600' }}">EN</a>
                </div>
                <button @click="open = !open" class="text-gray-700 hover:text-gray-900 p-2">
                    <svg x-show="!open" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="open" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="open" x-transition class="md:hidden border-t">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('home') }}" class="flex items-center gap-3 py-3 text-gray-800 hover:text-indigo-600 font-bold text-base">
                <x-heroicon name="home" class="w-5 h-5" />{{ __('messages.nav.home') }}
            </a>
            <a href="{{ route('books.browse') }}" class="flex items-center gap-3 py-3 text-gray-800 hover:text-indigo-600 font-bold text-base">
                <x-heroicon name="book-open" class="w-5 h-5" />{{ __('messages.nav.books') }}
            </a>
            <a href="{{ route('notes.browse') }}" class="flex items-center gap-3 py-3 text-gray-800 hover:text-indigo-600 font-bold text-base">
                <x-heroicon name="document-text" class="w-5 h-5" />{{ __('messages.nav.notes') }}
            </a>
            @auth
                <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 py-3 font-bold text-base {{ request()->routeIs('student.dashboard') ? 'text-indigo-600' : 'text-gray-800 hover:text-indigo-600' }}">
                    <x-heroicon name="squares-2x2" class="w-5 h-5" />{{ __('messages.nav.my_dashboard') }}
                </a>
                <a href="{{ route('student.books.index') }}" class="flex items-center gap-3 py-3 font-bold text-base {{ request()->routeIs('student.books.*') ? 'text-indigo-600' : 'text-gray-800 hover:text-indigo-600' }}">
                    <x-heroicon name="book-open" class="w-5 h-5" />{{ __('messages.nav.my_books') }}
                </a>
                <a href="{{ route('student.notes.index') }}" class="flex items-center gap-3 py-3 font-bold text-base {{ request()->routeIs('student.notes.*') ? 'text-indigo-600' : 'text-gray-800 hover:text-indigo-600' }}">
                    <x-heroicon name="document-text" class="w-5 h-5" />{{ __('messages.nav.my_notes') }}
                </a>
                <a href="{{ route('student.transactions.index') }}" class="flex items-center gap-3 py-3 font-bold text-base {{ request()->routeIs('student.transactions.*') ? 'text-indigo-600' : 'text-gray-800 hover:text-indigo-600' }}">
                    <x-heroicon name="arrows-right-left" class="w-5 h-5" />{{ app()->getLocale() === 'ar' ? 'التبادل والطلبات' : 'Exchanges & Requests' }}
                </a>
                <a href="{{ route('student.profile.edit') }}" class="flex items-center gap-3 py-3 font-bold text-base {{ request()->routeIs('student.profile.*') ? 'text-indigo-600' : 'text-gray-800 hover:text-indigo-600' }}">
                    <x-heroicon name="cog" class="w-5 h-5" />{{ __('messages.nav.my_account') }}
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 py-3 text-red-600 hover:text-red-800 font-bold text-base w-full">
                        <x-heroicon name="arrow-left-on-rectangle" class="w-5 h-5" />{{ __('messages.nav.logout') }}
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="flex items-center gap-3 py-3 text-indigo-600 font-bold text-base">
                    <x-heroicon name="arrow-right-on-rectangle" class="w-5 h-5" />{{ __('messages.nav.login') }}
                </a>
            @endauth
        </div>
    </div>
</nav>