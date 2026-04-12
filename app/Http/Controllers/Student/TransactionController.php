<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        // جلب العمليات التي يكون فيها الطالب هو المالك (الطلبات الواردة)
        // أو هو الطالب (الطلبات الصادرة)
        $transactions = Transaction::where('owner_id', $userId)
                                   ->orWhere('requester_id', $userId)
                                   ->latest()
                                   ->get();

        return view('student.transactions.index', compact('transactions'));
    }

    // تستخدم عندما يضغط الطالب على زر "طلب الكتاب" في واجهة الموقع
    public function store(Request $request)
    {
        $request->validate(['book_id' => 'required|exists:books,id']);
        $book = Book::findOrFail($request->book_id);

        // منع الطالب من طلب كتابه الخاص!
        if ($book->user_id === Auth::id()) {
            return back()->with('error', 'لا يمكنك طلب كتابك الخاص.');
        }

        Transaction::create([
            'book_id' => $book->id,
            'requester_id' => Auth::id(),
            'owner_id' => $book->user_id,
            'status' => 'pending'
        ]);

        return redirect()->route('student.transactions.index')->with('success', 'تم إرسال الطلب لمالك الكتاب.');
    }

    // تحديث حالة الطلب (قبول، تحديد موعد، إكمال، إلغاء)
    public function update(Request $request, Transaction $transaction)
    {
        // حماية: فقط طرفي العملية يمكنهما التعديل
        if ($transaction->owner_id !== Auth::id() && $transaction->requester_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:accepted,completed,cancelled',
            'meeting_date' => 'nullable|date',
            'meeting_time' => 'nullable',
            'meeting_location' => 'nullable|string'
        ]);

        $transaction->update($validated);

        // إذا اكتملت العملية، نجعل حالة الكتاب "مباع/مسلّم"
        if($validated['status'] == 'completed'){
            $transaction->book->update(['status' => 'sold']);
        }

        return back()->with('success', 'تم تحديث حالة الطلب.');
    }
}