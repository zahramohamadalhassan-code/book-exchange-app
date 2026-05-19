<footer class="bg-gray-800 text-gray-300 mt-auto">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <x-logo size="sm" color="white" class="mb-2" />
                <p class="text-sm leading-relaxed">{{ __('messages.footer.about_desc') }}</p>
            </div>

            <div>
                <h3 class="text-white font-bold text-lg mb-3">{{ __('messages.footer.quick_links') }}</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-white transition">{{ __('messages.footer.home') }}</a></li>
                    <li><a href="{{ route('books.browse') }}" class="hover:text-white transition">{{ __('messages.footer.browse_books') }}</a></li>
                    <li><a href="{{ route('notes.browse') }}" class="hover:text-white transition">{{ __('messages.footer.digital_summaries') }}</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-bold text-lg mb-3">{{ __('messages.footer.contact_us') }}</h3>
                <p class="text-sm flex items-center gap-2"><x-heroicon name="envelope" class="w-4 h-4" /> support@bookexchange.edu</p>
                <p class="text-sm mt-1 flex items-center gap-2"><x-heroicon name="device-phone-mobile" class="w-4 h-4" /> +963 900 000 000</p>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-6 pt-4 text-center text-sm">
            <p>&copy; {{ date('Y') }} {{ __('messages.footer.copyright') }}</p>
        </div>
    </div>
</footer>
