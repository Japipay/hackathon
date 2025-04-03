<?php

namespace App\Http\Controllers;

use App\Models\Summary;
use App\Models\Flashcard;
use Illuminate\Support\Facades\Auth;

class DashboardController
{
    public function index()
    {
        // Fetch the recent summaries for the logged-in user
        $summaries = Summary::where('user_id', auth()->id())->latest()->take(5)->get();
        
        // Fetch the recent flashcards for the logged-in user
        $flashcards = Flashcard::where('user_id', auth()->id())->latest()->get()->groupBy(function($flashcard) {
            return $flashcard->created_at->format('Y-m-d H:i'); // or any specific format
        });

        $flashcards = $flashcards->take(5);

        // dd($flashcards);

        // Pass the summaries and flashcards to the view
        return view('dashboard', compact('summaries', 'flashcards'));
    }
}
