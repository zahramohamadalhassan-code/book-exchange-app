<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\DigitalNote;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $latestBooks = Book::with(['user', 'category'])
            ->approved()
            ->available()
            ->latest()
            ->take(8)
            ->get();

        $latestNotes = DigitalNote::with(['user', 'category'])
            ->approved()
            ->latest()
            ->take(4)
            ->get();

        $stats = [
            'books_count' => Book::approved()->count(),
            'notes_count' => DigitalNote::approved()->count(),
            'users_count' => \App\Models\User::count(),
        ];

        return view('home', compact('latestBooks', 'latestNotes', 'stats'));
    }
}
