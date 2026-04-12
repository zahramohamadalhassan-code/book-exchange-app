<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Auth::user()->favorites()->with(['book', 'digitalNote'])->get();
        return view('student.favorites.index', compact('favorites'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'nullable|exists:books,id',
            'note_id' => 'nullable|exists:digital_notes,id',
        ]);

        // التحقق من عدم إضافة نفس العنصر مرتين
        $exists = Auth::user()->favorites()
            ->where('book_id', $request->book_id)
            ->where('note_id', $request->note_id)
            ->exists();

        if (!$exists) {
            Auth::user()->favorites()->create($request->all());
        }

        return back()->with('success', 'تمت الإضافة للمفضلة.');
    }

    public function destroy(Favorite $favorite)
    {
        if ($favorite->user_id !== Auth::id()) abort(403);
        $favorite->delete();
        return back()->with('success', 'تم الحذف من المفضلة.');
    }
}