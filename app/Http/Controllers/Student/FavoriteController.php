<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Book;
use App\Models\DigitalNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Auth::user()->favorites()->with('favoritable')->get();
        return view('student.favorites.index', compact('favorites'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'favoritable_id' => 'required|integer',
            'favoritable_type' => 'required|in:book,note',
        ]);

        // تحديد نوع الـ Model
        $type = $request->favoritable_type === 'book'
            ? Book::class
            : DigitalNote::class;

        // التحقق من عدم إضافة نفس العنصر مرتين
        $exists = Auth::user()->favorites()
            ->where('favoritable_id', $request->favoritable_id)
            ->where('favoritable_type', $type)
            ->exists();

        if (!$exists) {
            Auth::user()->favorites()->create([
                'favoritable_id' => $request->favoritable_id,
                'favoritable_type' => $type,
            ]);
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
