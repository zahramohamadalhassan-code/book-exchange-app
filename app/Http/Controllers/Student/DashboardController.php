<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // إحضار إحصائيات الطالب
        $booksCount = $user->books()->count();
        $notesCount = $user->digitalNotes()->count();
        
        // طلبات التسليم التي يحتاج الطالب للرد عليها (هو مالك الكتاب والطلب قيد الانتظار)
        $pendingRequests = \App\Models\Transaction::where('owner_id', $user->id)
                                                  ->where('status', 'pending')
                                                  ->count();

        return view('student.dashboard', compact('booksCount', 'notesCount', 'pendingRequests'));
    }
}