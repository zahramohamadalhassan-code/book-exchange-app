<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\DigitalNote;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class BrowseController extends Controller
{
    /**
     * تصفح الكتب المتاحة
     */
    public function books(Request $request)
    {
        $query = Book::with(['user', 'category'])->approved()->available();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('author', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('offer_type')) {
            $query->where('offer_type', $request->offer_type);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $books = $query->latest()->paginate(12);
        $categories = Category::all();

        return view('books.index', compact('books', 'categories'));
    }

    /**
     * عرض تفاصيل كتاب محدد
     */
    public function showBook(Book $book)
    {
        // التحقق من أن الكتاب معتمد ومتاح
        if ($book->moderation_status !== 'approved' || $book->status !== 'available') {
            abort(404, 'الكتاب غير متاح أو في انتظار المراجعة.');
        }

        $book->load(['user', 'category']);

        $myBooks = collect();
        if (auth()->check()) {
            $myBooks = Book::where('user_id', auth()->id())
                           ->where('status', 'available')
                           ->where('moderation_status', 'approved')
                           ->get();
        }

        $relatedBooks = Book::with(['user', 'category'])
                            ->where('category_id', $book->category_id)
                            ->where('id', '!=', $book->id)
                            ->approved()
                            ->available()
                            ->latest()
                            ->take(4)
                            ->get();

        return view('books.show', compact('book', 'myBooks', 'relatedBooks'));
    }

    /**
     * تصفح الملخصات الرقمية
     */
    public function notes(Request $request)
    {
        $query = DigitalNote::with(['user', 'category'])->approved();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $notes = $query->latest()->paginate(12);
        $categories = Category::all();

        return view('notes.index', compact('notes', 'categories'));
    }

    /**
     * عرض تفاصيل ملخص محدد
     */
    public function showNote(DigitalNote $note)
    {
        // التحقق من أن الملخص معتمد
        if ($note->moderation_status !== 'approved') {
            abort(404, 'الملخص غير متاح أو في انتظار المراجعة.');
        }

        $note->load(['user', 'category']);
        return view('notes.show', compact('note'));
    }

    /**
     * عرض تقييمات مستخدم محدد
     */
    public function userRatings(User $user)
    {
        $ratings = $user->ratingsReceived()
                        ->with('reviewer')
                        ->latest()
                        ->paginate(12);

        return view('users.ratings', compact('user', 'ratings'));
    }
}
