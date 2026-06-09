<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Services\GeminiAiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    private GeminiAiService $aiService;

    public function __construct(GeminiAiService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index(Request $request)
    {
        $query = Auth::user()->books()->with('category');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }

        if ($request->filled('offer_type')) {
            $query->where('offer_type', $request->input('offer_type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('moderation_status')) {
            $query->where('moderation_status', $request->input('moderation_status'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'oldest' => $query->oldest(),
            'title_asc' => $query->orderBy('title', 'asc'),
            'title_desc' => $query->orderBy('title', 'desc'),
            'price_asc' => $query->orderByRaw('COALESCE(price, 0) asc'),
            'price_desc' => $query->orderByRaw('COALESCE(price, 0) desc'),
            default => $query->latest(),
        };

        $books = $query->paginate(10)->appends($request->query());
        $categories = Category::all();

        return view('student.books.index', compact('books', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('student.books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'pages_count' => 'nullable|integer|min:1',
            'condition' => 'required|in:excellent,good,fair,poor',
            'offer_type' => 'required|in:sale,exchange,donate',
            'exchange_for' => 'required_if:offer_type,exchange|nullable|string|max:255',
            'price' => 'nullable|numeric',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'content_description' => 'nullable|string|max:2000',
            'payment_method' => 'nullable|string|in:cash_on_delivery,syriatel_cash,mtn_cash,bank_transfer,cham_cash',
        ]);

        // رفع صورة الغلاف + فحص المحتوى بالذكاء الاصطناعي
        if ($request->hasFile('cover_image')) {
            $validated['cover_image_url'] = $request->file('cover_image')->store('books/covers', 'public');

            $fullPath = storage_path('app/public/' . $validated['cover_image_url']);
            $details = $this->aiService->extractBookDetails($fullPath);

            if (isset($details['rejected']) && $details['rejected']) {
                @unlink($fullPath);
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['cover_image' => $details['reject_reason'] ?: __('messages.moderation.image_rejected')]);
            }
        }

        // إنشاء الكتاب وربطه بالطالب الحالي مع الموافقة التلقائية
        $validated['moderation_status'] = 'approved';
        Auth::user()->books()->create($validated);

        return redirect()->route('student.books.index')->with('success', 'تم إضافة الكتاب بنجاح.');
    }

    public function edit(Book $book)
    {
        // حماية: تأكد أن الكتاب يخص الطالب الحالي
        if ($book->user_id !== Auth::id()) abort(403);

        $categories = Category::all();
        return view('student.books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        if ($book->user_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'pages_count' => 'nullable|integer|min:1',
            'condition' => 'required|in:excellent,good,fair,poor',
            'offer_type' => 'required|in:sale,exchange,donate',
            'exchange_for' => 'required_if:offer_type,exchange|nullable|string|max:255',
            'price' => 'nullable|numeric',
            'payment_method' => 'nullable|string|in:cash_on_delivery,syriatel_cash,mtn_cash,bank_transfer,cham_cash',
            'status' => 'required|in:available,pending,sold'
        ]);

        $book->update($validated);
        return redirect()->route('student.books.index')->with('success', 'تم التحديث بنجاح.');
    }

    public function destroy(Book $book)
    {
        if ($book->user_id !== Auth::id()) abort(403);
        $book->delete();
        return redirect()->route('student.books.index')->with('success', 'تم الحذف.');
    }
}