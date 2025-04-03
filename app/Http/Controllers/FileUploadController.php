<?php

namespace App\Http\Controllers;

use App\Models\Summary;
use OpenAI\Laravel\Facades\OpenAI;

class FileUploadController extends Controller
{
    public function generateSummary($uploadId)
    {
        $upload = UserUpload::findOrFail($uploadId);

        // Send text to OpenAI
        $response = OpenAI::completions()->create([
            'model' => 'gpt-4',
            'prompt' => "Summarize this text:\n" . $upload->original_text,
            'max_tokens' => 200,
        ]);

        $summaryText = $response['choices'][0]['text'] ?? 'Summary not available.';

        // Save to database
        Summary::create([
            'user_id' => auth()->id(),
            'upload_id' => $uploadId,
            'summary_text' => trim($summaryText),
        ]);

        return back()->with('success', 'Summary generated successfully!');
    }
}
