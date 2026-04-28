{{-- التذييل --}}
<footer class="bg-gray-800 text-gray-300 mt-auto">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- عن المنصة --}}
            <div>
                <h3 class="text-white font-bold text-lg mb-3">📚 منصة تبادل الكتب</h3>
                <p class="text-sm leading-relaxed">منصة مخصصة لطلاب الجامعة لتبادل وبيع والتبرع بالكتب الجامعية ومشاركة الملخصات الرقمية.</p>
            </div>

            {{-- روابط سريعة --}}
            <div>
                <h3 class="text-white font-bold text-lg mb-3">روابط سريعة</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-white transition">الرئيسية</a></li>
                    <li><a href="{{ route('books.browse') }}" class="hover:text-white transition">تصفح الكتب</a></li>
                    <li><a href="{{ route('notes.browse') }}" class="hover:text-white transition">الملخصات الرقمية</a></li>
                </ul>
            </div>

            {{-- تواصل --}}
            <div>
                <h3 class="text-white font-bold text-lg mb-3">تواصل معنا</h3>
                <p class="text-sm">📧 support@bookexchange.edu</p>
                <p class="text-sm mt-1">📱 +963 900 000 000</p>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-6 pt-4 text-center text-sm">
            <p>&copy; {{ date('Y') }} منصة تبادل الكتب الجامعية. جميع الحقوق محفوظة.</p>
        </div>
    </div>
</footer>
