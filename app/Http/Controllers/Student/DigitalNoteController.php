<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DigitalNote;
use App\Models\Category;
use App\Services\GeminiAiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DigitalNoteController extends Controller
{
    private GeminiAiService $aiService;

    public function __construct(GeminiAiService $aiService)
    {
        $this->aiService = $aiService;
    }
    public function index(Request $request)
    {
        $query = Auth::user()->digitalNotes()->with('category');

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->filled('moderation_status')) {
            $query->where('moderation_status', $request->input('moderation_status'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'oldest' => $query->oldest(),
            'title_asc' => $query->orderBy('title', 'asc'),
            'title_desc' => $query->orderBy('title', 'desc'),
            default => $query->latest(),
        };

        $notes = $query->paginate(10)->appends($request->query());
        $categories = Category::all();

        return view('student.notes.index', compact('notes', 'categories'));
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

        $fullPath = storage_path('app/public/' . $validated['pdf_file_url']);
        $moderation = $this->aiService->moderatePdf($fullPath);

        if (!$moderation['safe']) {
            @unlink($fullPath);
            return redirect()->back()
                ->withInput()
                ->withErrors(['pdf_file' => $moderation['reason'] ?: __('messages.moderation.image_rejected')]);
        }

        $validated['moderation_status'] = 'approved';
        Auth::user()->digitalNotes()->create($validated);

        return redirect()->route('student.notes.index')->with('success', 'تم رفع الملخص بنجاح.');
    }

    public function destroy(DigitalNote $note)
    {
        if ($note->user_id !== Auth::id()) abort(403);
        $note->delete();
        return redirect()->route('student.notes.index')->with('success', 'تم حذف الملخص.');
    }

    public function edit(DigitalNote $note)
    {
        if ($note->user_id !== Auth::id()) abort(403);
        $categories = Category::all();
        return view('student.notes.edit', compact('note', 'categories'));
    }

    public function update(Request $request, DigitalNote $note)
    {
        if ($note->user_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pdf_file' => 'nullable|mimes:pdf|max:10000'
        ]);

        if ($request->hasFile('pdf_file')) {
            $validated['pdf_file_url'] = $request->file('pdf_file')->store('notes/pdfs', 'public');

            $fullPath = storage_path('app/public/' . $validated['pdf_file_url']);
            $moderation = $this->aiService->moderatePdf($fullPath);

            if (!$moderation['safe']) {
                @unlink($fullPath);
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['pdf_file' => $moderation['reason'] ?: __('messages.moderation.image_rejected')]);
            }
        }

        $note->update($validated);

        return redirect()->route('student.notes.index')->with('success', 'تم تحديث الملخص بنجاح.');
    }
}