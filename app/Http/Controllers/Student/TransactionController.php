<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = Transaction::query();

        // فلترة بنوع الطلب (وارد أو صادر)
        $type = $request->input('type');
        if ($type === 'incoming') {
            $query->where('owner_id', $userId);
        } elseif ($type === 'outgoing') {
            $query->where('requester_id', $userId);
        } else {
            $query->where(function($q) use ($userId) {
                $q->where('owner_id', $userId)
                  ->orWhere('requester_id', $userId);
            });
        }

        // فلترة بالحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة نصية (بحث باسم الكتاب أو اسم الطرف الآخر)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('book', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            })->orWhereHas('requester', function($q) use ($search, $userId) {
                // البحث في اسم المشتري (إذا كنت أنت المالك)
                $q->where('full_name', 'like', "%{$search}%");
            })->orWhereHas('owner', function($q) use ($search, $userId) {
                // البحث في اسم المالك (إذا كنت أنت المشتري)
                $q->where('full_name', 'like', "%{$search}%");
            });

            // ضمان أن النتائج بعد البحث النصي لا تزال تنتمي للمستخدم الحالي
            $query->where(function($q) use ($userId) {
                $q->where('owner_id', $userId)
                  ->orWhere('requester_id', $userId);
            });
        }

        $transactions = $query->latest()->paginate(10);

        return view('student.transactions.index', compact('transactions'));
    }

    // تستخدم عندما يضغط الطالب على زر "طلب الكتاب" في واجهة الموقع
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'offered_book_id' => 'nullable|exists:books,id',
            'meeting_date' => 'nullable|date',
            'meeting_time' => 'nullable',
            'meeting_location' => 'nullable|string',
        ]);
        $book = Book::findOrFail($validated['book_id']);



        // منع الطالب من طلب كتابه الخاص!
        if ($book->user_id === Auth::id()) {
            return back()->with('error', 'لا يمكنك طلب كتابك الخاص.');
        }

        // منع تكرار الطلب لنفس الكتاب إذا كان هناك طلب قيد الانتظار أو مقبول
        $existingTransaction = Transaction::where('book_id', $book->id)
            ->where('requester_id', Auth::id())
            ->whereIn('status', ['pending', 'accepted'])
            ->first();

        if ($existingTransaction) {
            return back()->with('error', 'لقد قمت بتقديم طلب لهذا الكتاب مسبقاً، وحالته حالياً: ' . ($existingTransaction->status === 'pending' ? 'قيد الانتظار' : 'مقبول') . '.');
        }

        Transaction::create([
            'book_id' => $book->id,
            'offered_book_id' => $validated['offered_book_id'] ?? null,
            'requester_id' => Auth::id(),
            'owner_id' => $book->user_id,
            'meeting_date' => $validated['meeting_date'] ?? null,
            'meeting_time' => $validated['meeting_time'] ?? null,
            'meeting_location' => $validated['meeting_location'] ?? null,
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

        // تحديث حالة الكتاب بناءً على حالة الطلب
        if ($validated['status'] === 'accepted') {
            // الكتاب أصبح محجوزاً
            $transaction->book->update(['status' => 'pending']);
        } elseif ($validated['status'] === 'completed') {
            // الكتاب تم تسليمه بنجاح
            $transaction->book->update(['status' => 'sold']);
        } elseif ($validated['status'] === 'cancelled') {
            // في حال الإلغاء نعود لجعله متاحاً
            $transaction->book->update(['status' => 'available']);
        }

        return back()->with('success', 'تم تحديث حالة الطلب.');
    }
}