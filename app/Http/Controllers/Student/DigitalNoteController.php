<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DigitalNote;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DigitalNoteController extends Controller
{
    public function index()
    {
        $notes = Auth::user()->digitalNotes()->latest()->paginate(10);
        return view('student.notes.index', compact('notes'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('student.notes.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pdf_file' => 'required|mimes:pdf|max:10000' // ملف PDF حصراً وبحجم أقصى 10 ميجا
        ]);

        $validated['pdf_file_url'] = $request->file('pdf_file')->store('notes/pdfs', 'public');

        Auth::user()->digitalNotes()->create($validated);

        return redirect()->route('student.notes.index')->with('success', 'تم رفع الملخص بنجاح.');
    }

    public function destroy(DigitalNote $note)
    {
        if ($note->user_id !== Auth::id()) abort(403);
        $note->delete();
        return redirect()->route('student.notes.index')->with('success', 'تم حذف الملخص.');
    }
}