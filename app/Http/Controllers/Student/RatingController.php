<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    /**
     * عرض جميع التقييمات التي كتبها الطالب أو التي حصل عليها.
     */
    public function index()
    {
        $userId = Auth::id();

        // التقييمات التي تلقاها الطالب من زملائه
        $receivedRatings = Rating::where('reviewed_user_id', $userId)->latest()->get();

        // التقييمات التي كتبها الطالب لزملائه
        $givenRatings = Rating::where('reviewer_id', $userId)->latest()->get();

        return view('student.ratings.index', compact('receivedRatings', 'givenRatings'));
    }

    /**
     * حفظ التقييم بعد إتمام عملية تبادل/تسليم كتاب
     */
    public function store(Request $request)
    {
        // 1. التحقق من صحة المدخلات (التقييم من 1 إلى 5 نجوم)
        $validated = $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'stars' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ]);

        $transaction = Transaction::findOrFail($request->transaction_id);
        $userId = Auth::id();

        // 2. حماية: التأكد أن الطالب هو أحد أطراف هذه العملية
        if ($transaction->owner_id !== $userId && $transaction->requester_id !== $userId) {
            abort(403, 'غير مصرح لك بتقييم هذه العملية لأنك لست طرفاً فيها.');
        }

        // 3. التحقق أن عملية التسليم اكتملت بالفعل
        if ($transaction->status !== 'completed') {
            return back()->with('error', 'لا يمكنك التقييم إلا بعد اكتمال عملية التسليم.');
        }

        // 4. التحقق من عدم تكرار التقييم لنفس العملية (لكي لا يكتب تقييمين لنفس الشخص)
        $existingRating = Rating::where('transaction_id', $transaction->id)
                                ->where('reviewer_id', $userId)
                                ->exists();

        if ($existingRating) {
            return back()->with('error', 'لقد قمت بتقييم هذه العملية مسبقاً.');
        }

        // 5. تحديد من هو الشخص المُقيَّم (الطرف الآخر في العملية)
        $reviewedUserId = ($transaction->owner_id === $userId) 
                          ? $transaction->requester_id 
                          : $transaction->owner_id;

        // 6. حفظ التقييم في قاعدة البيانات
        Rating::create([
            'transaction_id' => $transaction->id,
            'reviewer_id' => $userId,
            'reviewed_user_id' => $reviewedUserId,
            'stars' => $validated['stars'],
            'comment' => $validated['comment']
        ]);

        return redirect()->route('student.ratings.index')->with('success', 'تم إرسال تقييمك بنجاح. شكراً لك!');
    }

    /**
     * حذف التقييم (يمكن للطالب حذف تقييم قام بكتابته)
     */
    public function destroy(Rating $rating)
    {
        // حماية: التأكد أن الطالب يحاول حذف تقييمه هو وليس تقييم شخص آخر
        if ($rating->reviewer_id !== Auth::id()) {
            abort(403);
        }

        $rating->delete();

        return back()->with('success', 'تم حذف التقييم.');
    }
}