<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\DigitalNote;
use App\Models\Category;
use Illuminate\Http\Request;

class BrowseController extends Controller
{
    /**
     * تصفح الكتب المعتمدة مع فلترة
     */
    public function books(Request $request)
    {
        $query = Book::with(['user', 'category'])
            ->approved()
            ->available();

        // فلترة حسب القسم
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // فلترة حسب نوع العرض
        if ($request->filled('offer_type')) {
            $query->where('offer_type', $request->offer_type);
        }

        // فلترة حسب الحالة
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        // البحث بالعنوان
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $books = $query->latest()->paginate(12);
        $categories = Category::all();

        return view('books.index', compact('books', 'categories'));
    }

    /**
     * عرض تفاصيل كتاب واحد
     */
    public function showBook(Book $book)
    {
        $book->load(['user', 'category', 'transactions']);

        // كتب مشابهة من نفس التصنيف
        $relatedBooks = Book::where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->approved()
            ->available()
            ->take(4)
            ->get();

        return view('books.show', compact('book', 'relatedBooks'));
    }

    /**
     * تصفح الملخصات الرقمية المعتمدة
     */
    public function notes(Request $request)
    {
        $query = DigitalNote::with(['user', 'category'])
            ->approved();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $notes = $query->latest()->paginate(12);
        $categories = Category::all();

        return view('notes.index', compact('notes', 'categories'));
    }

    /**
     * عرض تفاصيل ملخص واحد
     */
    public function showNote(DigitalNote $note)
    {
        $note->load(['user', 'category']);
        return view('notes.show', compact('note'));
    }
}
