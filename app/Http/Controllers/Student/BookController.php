<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    public function index()
    {
        // عرض كتب الطالب الحالي فقط
        $books = Auth::user()->books()->latest()->paginate(10);
        return view('student.books.index', compact('books'));
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
            'condition' => 'required|in:excellent,good,fair,poor',
            'offer_type' => 'required|in:sale,exchange,donate',
            'price' => 'nullable|numeric',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // رفع الصورة إذا وجدت
        if ($request->hasFile('cover_image')) {
            $validated['cover_image_url'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        // إنشاء الكتاب وربطه بالطالب الحالي
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
            'condition' => 'required|in:excellent,good,fair,poor',
            'offer_type' => 'required|in:sale,exchange,donate',
            'price' => 'nullable|numeric',
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