<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flashcard;
use Smalot\PdfParser\Parser;
use OpenAI;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PdfFlashcardController extends Controller
{
    public function showUploadForm()
    {
        return view('flashcard-upload'); // Ensure this view exists
    }

    public function generateFlashcards(Request $request)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:10240',
            'flashcard_count' => 'required|integer|min:3|max:20',
            'difficulty' => 'required|in:basic,intermediate,advanced'
        ]);

        try {
            $file = $request->file('pdf');
            $pdfText = $this->extractTextFromPdf($file);
            
            if (empty($pdfText)) {
                return back()->with('error', 'Failed to extract text from the PDF.');
            }

            $flashcards = $this->generateFlashcardsWithOpenAI(
                $pdfText,
                $request->input('flashcard_count'),
                $request->input('difficulty')
            );

            // Save flashcards to database if valid
            if (!empty($flashcards) && is_array($flashcards)) {
                foreach ($flashcards as $card) {
                    if (!isset($card['question'], $card['answer'])) continue;

                    Flashcard::create([
                        'user_id' => Auth::id(),
                        'question' => $card['question'],
                        'answer' => $card['answer']
                    ]);
                }
            }

            return redirect()->route('flashcards.index')->with('success', 'Flashcards generated successfully.');
            // return redirect()->route('dashboard')->with('success', 'Flashcards generated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error generating flashcards: ' . $e->getMessage());
        }
    }

    private function extractTextFromPdf($file)
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($file->getPathname());
        $text = preg_replace('/\s+/', ' ', $pdf->getText());
        return trim($text);
    }

    private function generateFlashcardsWithOpenAI($text, $count, $difficulty)
    {
        $text = substr($text, 0, 12000);
        $client = OpenAI::client(env('OPENAI_API_KEY'));

        $response = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "Generate exactly $count flashcards at $difficulty level in Q&A format."
                ],
                [
                    'role' => 'user',
                    'content' => "Generate $count $difficulty flashcards from this text:\n\n" . $text
                ]
            ],
            'temperature' => $difficulty === 'basic' ? 0.2 : ($difficulty === 'advanced' ? 0.4 : 0.3),
        ]);

        return $this->parseFlashcards($response->choices[0]->message->content ?? '');
    }

    private function parseFlashcards($flashcardsText)
    {
        $flashcards = [];
        $pairs = preg_split("/\n\n+/", trim($flashcardsText)); // Split by double line breaks

        foreach ($pairs as $pair) {
            if (preg_match('/Q:\s*(.+?)\nA:\s*(.+)/s', $pair, $matches)) {
                $flashcards[] = [
                    'question' => trim($matches[1]),
                    'answer' => trim($matches[2])
                ];
            }
        }

        return !empty($flashcards) ? $flashcards : [['question' => 'No flashcards generated.', 'answer' => 'Try again.']];
    }
    
    public function index()
    {
        $flashcards = Flashcard::where('user_id', Auth::id())->latest()->get()->groupBy(function($flashcard) {
            return $flashcard->created_at->format('Y-m-d H:i'); // or any specific format
        });
        $flashcards = $flashcards[$flashcards->keys()->first()];
        return view('flashcards', compact('flashcards'));
    }
    
    public function viewSpecific($timestamp)
    {
        $timestamp = Carbon::parse($timestamp);  // Assuming you are using Carbon for DateTime handling

    // Fetch flashcards that match the exact created_at timestamp
        $flashcard = Flashcard::where('user_id', Auth::id())
                            ->whereDate('created_at', '=', $timestamp->toDateString()) // Comparing just the date part
                            ->whereTime('created_at', '=', $timestamp->toTimeString()) // Comparing just the time part
                            ->get()->first();


        // $flashcard->created_at
        $flashcards = Flashcard::where('user_id', Auth::id())->latest()->get()->groupBy(function($flashcard) {
            return $flashcard->created_at->format('Y-m-d H:i'); // or any specific format
        });
        $flashcards = $flashcards[$flashcards->keys()->first()];

        return view('flashcards', compact('flashcards'));
    }
    
}
