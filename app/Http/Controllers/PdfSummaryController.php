<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use OpenAI;
use App\Models\Summary;
use App\Models\UserUpload;


class PdfSummaryController extends Controller
{
    public function showUploadForm()
    {
        return view('summary-upload'); // Create summary_upload.blade.php
    }

    public function uploadAndSummarize(Request $request)
{
    $request->validate([
        'pdf' => 'required|mimes:pdf|max:10240', // 10MB max
    ]);

    try {
        $file = $request->file('pdf');
        $pdfText = $this->extractTextFromPdf($file);
        $summary = $this->summarizeWithOpenAI($pdfText);

        // Save the upload record first
        $title = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

// Save the upload record
$upload = UserUpload::create([
    'user_id' => auth()->id(),
    'file_path' => $file->store('uploads'),
    'title' => $title,  // Set the title
]);

        // Save the summary to the database
        Summary::create([
            'user_id' => auth()->id(),
            'upload_id' => $upload->id, // Associate with the uploaded PDF
            'summary_text' => $summary, // The generated summary
        ]);

        return view('summary', [
            'originalText' => $pdfText,
            'summary' => $summary,
            'filename' => $file->getClientOriginalName()
        ]);

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error processing PDF: ' . $e->getMessage());
    }
}

    private function extractTextFromPdf($file)
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($file->getPathname());
        $text = preg_replace('/\s+/', ' ', $pdf->getText());
        return trim($text);
    }

    public function viewSummary($id)
{
    $summary = Summary::findOrFail($id);
    return view('summary-view', compact('summary'));
}

public function show($id)
{
    // Retrieve the summary by ID
    $summary = Summary::findOrFail($id);

    // Return a view to display the summary
    return view('summary.show', compact('summary'));
}


    private function summarizeWithOpenAI($text)
    {
        $text = substr($text, 0, 12000);
        $apiKey = env('OPENAI_API_KEY');

        if (!$apiKey) {
            throw new \Exception('Missing OpenAI API Key. Check your .env file.');
        }

        $client = OpenAI::client($apiKey);
        $response = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'Summarize PDF content into bullet points.'],
                ['role' => 'user', 'content' => "Summarize this text:\n\n" . $text]
            ],
            'temperature' => 0.3,
        ]);

        return $response->choices[0]->message->content;
    }
}
