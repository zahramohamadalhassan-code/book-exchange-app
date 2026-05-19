<nav x-data="{ open: false }" class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <x-logo size="sm" :text="false" />
                    <span class="text-xl font-bold text-indigo-600">{{ __('messages.app_name') }}</span>
                </a>
            </div>

            <div class="hidden md:flex items-center gap-5">
                <a href="{{ route('home') }}" class="flex items-center gap-1.5 text-gray-700 hover:text-indigo-600 transition font-medium whitespace-nowrap {{ request()->routeIs('home') ? 'text-indigo-600' : '' }}">
                    <x-heroicon name="home" class="w-4 h-4" />
                    {{ __('messages.nav.home') }}
                </a>
                <a href="{{ route('books.browse') }}" class="flex items-center gap-1.5 text-gray-700 hover:text-indigo-600 transition font-medium whitespace-nowrap {{ request()->routeIs('books.*') ? 'text-indigo-600' : '' }}">
                    <x-heroicon name="book-open" class="w-4 h-4" />
                    {{ __('messages.nav.books') }}
                </a>
                <a href="{{ route('notes.browse') }}" class="flex items-center gap-1.5 text-gray-700 hover:text-indigo-600 transition font-medium whitespace-nowrap {{ request()->routeIs('notes.*') ? 'text-indigo-600' : '' }}">
                    <x-heroicon name="document-text" class="w-4 h-4" />
                    {{ __('messages.nav.notes') }}
                </a>

                @auth
                    <a href="{{ route('student.dashboard') }}" class="flex items-center gap-1.5 text-gray-700 hover:text-indigo-600 transition font-medium whitespace-nowrap {{ request()->routeIs('student.dashboard') ? 'text-indigo-600' : '' }}">
                        <x-heroicon name="squares-2x2" class="w-4 h-4" />
                        {{ __('messages.nav.my_dashboard') }}
                    </a>
                    <a href="{{ route('student.books.index') }}" class="flex items-center gap-1.5 text-gray-700 hover:text-indigo-600 transition font-medium whitespace-nowrap {{ request()->routeIs('student.books.*') ? 'text-indigo-600' : '' }}">
                        <x-heroicon name="book-open" class="w-4 h-4" />
                        {{ __('messages.nav.my_books') }}
                    </a>
                    <a href="{{ route('student.notes.index') }}" class="flex items-center gap-1.5 text-gray-700 hover:text-indigo-600 transition font-medium whitespace-nowrap {{ request()->routeIs('student.notes.*') ? 'text-indigo-600' : '' }}">
                        <x-heroicon name="document-text" class="w-4 h-4" />
                        {{ __('messages.nav.my_notes') }}
                    </a>
                    <a href="{{ route('student.transactions.index') }}" class="flex items-center gap-1.5 text-gray-700 hover:text-indigo-600 transition font-medium whitespace-nowrap {{ request()->routeIs('student.transactions.*') ? 'text-indigo-600' : '' }}">
                        <x-heroicon name="arrows-right-left" class="w-4 h-4" />
                        {{ app()->getLocale() === 'ar' ? 'التبادل والطلبات' : 'Exchanges & Requests' }}
                    </a>
                    <a href="{{ route('student.profile.edit') }}" class="flex items-center gap-1.5 text-gray-700 hover:text-indigo-600 transition font-medium whitespace-nowrap {{ request()->routeIs('student.profile.*') ? 'text-indigo-600' : '' }}">
                        <x-heroicon name="cog" class="w-4 h-4" />
                        {{ __('messages.nav.my_account') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center gap-1.5 text-red-500 hover:text-red-700 transition font-medium whitespace-nowrap">
                            <x-heroicon name="arrow-left-on-rectangle" class="w-4 h-4" />
                            {{ __('messages.nav.logout') }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-medium whitespace-nowrap">{{ __('messages.nav.login') }}</a>
                    <a href="{{ route('register') }}" class="border border-indigo-600 text-indigo-600 px-4 py-2 rounded-lg hover:bg-indigo-50 transition font-medium whitespace-nowrap">{{ __('messages.nav.register') }}</a>
                @endauth

                <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden ms-2">
                    <a href="{{ route('locale.switch', 'ar') }}" class="px-3 py-1 text-sm font-medium {{ app()->getLocale() === 'ar' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100' }} transition">عربي</a>
                    <a href="{{ route('locale.switch', 'en') }}" class="px-3 py-1 text-sm font-medium {{ app()->getLocale() === 'en' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100' }} transition">EN</a>
                </div>
            </div>

            <div class="md:hidden flex items-center gap-2">
                <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                    <a href="{{ route('locale.switch', 'ar') }}" class="px-2 py-1 text-xs font-medium {{ app()->getLocale() === 'ar' ? 'bg-indigo-600 text-white' : 'text-gray-600' }}">عربي</a>
                    <a href="{{ route('locale.switch', 'en') }}" class="px-2 py-1 text-xs font-medium {{ app()->getLocale() === 'en' ? 'bg-indigo-600 text-white' : 'text-gray-600' }}">EN</a>
                </div>
                <button @click="open = !open" class="text-gray-600 hover:text-gray-900 p-2">
                    <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="open" x-transition class="md:hidden border-t">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('home') }}" class="flex items-center gap-2 py-2 text-gray-700 hover:text-indigo-600">
                <x-heroicon name="home" class="w-5 h-5" />{{ __('messages.nav.home') }}
            </a>
            <a href="{{ route('books.browse') }}" class="flex items-center gap-2 py-2 text-gray-700 hover:text-indigo-600">
                <x-heroicon name="book-open" class="w-5 h-5" />{{ __('messages.nav.books') }}
            </a>
            <a href="{{ route('notes.browse') }}" class="flex items-center gap-2 py-2 text-gray-700 hover:text-indigo-600">
                <x-heroicon name="document-text" class="w-5 h-5" />{{ __('messages.nav.notes') }}
            </a>
            @auth
                <a href="{{ route('student.dashboard') }}" class="flex items-center gap-2 py-2 {{ request()->routeIs('student.dashboard') ? 'text-indigo-600 font-medium' : 'text-gray-700 hover:text-indigo-600' }}">
                    <x-heroicon name="squares-2x2" class="w-5 h-5" />{{ __('messages.nav.my_dashboard') }}
                </a>
                <a href="{{ route('student.books.index') }}" class="flex items-center gap-2 py-2 {{ request()->routeIs('student.books.*') ? 'text-indigo-600 font-medium' : 'text-gray-700 hover:text-indigo-600' }}">
                    <x-heroicon name="book-open" class="w-5 h-5" />{{ __('messages.nav.my_books') }}
                </a>
                <a href="{{ route('student.notes.index') }}" class="flex items-center gap-2 py-2 {{ request()->routeIs('student.notes.*') ? 'text-indigo-600 font-medium' : 'text-gray-700 hover:text-indigo-600' }}">
                    <x-heroicon name="document-text" class="w-5 h-5" />{{ __('messages.nav.my_notes') }}
                </a>
                <a href="{{ route('student.transactions.index') }}" class="flex items-center gap-2 py-2 {{ request()->routeIs('student.transactions.*') ? 'text-indigo-600 font-medium' : 'text-gray-700 hover:text-indigo-600' }}">
                    <x-heroicon name="arrows-right-left" class="w-5 h-5" />{{ app()->getLocale() === 'ar' ? 'التبادل والطلبات' : 'Exchanges & Requests' }}
                </a>
                <a href="{{ route('student.profile.edit') }}" class="flex items-center gap-2 py-2 {{ request()->routeIs('student.profile.*') ? 'text-indigo-600 font-medium' : 'text-gray-700 hover:text-indigo-600' }}">
                    <x-heroicon name="cog" class="w-5 h-5" />{{ __('messages.nav.my_account') }}
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 py-2 text-red-500 hover:text-red-700 font-medium w-full">
                        <x-heroicon name="arrow-left-on-rectangle" class="w-5 h-5" />{{ __('messages.nav.logout') }}
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="flex items-center gap-2 py-2 text-indigo-600 font-medium">
                    <x-heroicon name="arrow-right-on-rectangle" class="w-5 h-5" />{{ __('messages.nav.login') }}
                </a>
            @endauth
        </div>
    </div>
</nav>
