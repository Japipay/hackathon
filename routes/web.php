<?php
//use App\Http\Controllers\PdfController;
use App\Http\Controllers\ProfileController;
// use App\Http\Controllers\QuizController;
//use App\Http\Controllers\FlashcardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfSummaryController;
use App\Http\Controllers\PdfFlashcardController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Dashboard route
Route::middleware(['auth', 'verified'])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/upload', [PdfSummaryController::class, 'showUploadForm'])->name('upload.form');
Route::post('/uploadAndSummarize', [PdfSummaryController::class, 'uploadAndSummarize'])->name('upload.summarize');
Route::get('/flashcard-upload', [PdfFlashcardController::class, 'showUploadForm'])->name('flashcard.upload');
Route::post('/generateflashcard', [PdfFlashcardController::class, 'generateFlashcards'])->name('generate.flashcards');

Route::get('/summary/{id}', [PdfSummaryController::class, 'viewSummary'])->name('summary.view');

Route::get('/flashcards', [PdfFlashcardController::class, 'index'])->name('flashcards.index');
Route::get('/flashcards-view/{id}', [PdfFlashcardController::class, 'viewSpecific'])->name('flashcards.view');

// Authenticated user routes (Profile management)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Quiz and Flashcard routes (within auth group)
    // Route::get('/quiz/create', [QuizController::class, 'create'])->name('quiz.create');
    // Route::post('/quiz', [QuizController::class, 'store'])->name('quizzes.store');
    
    // Route::get('/flashcard/create', [FlashcardController::class, 'create'])->name('flashcard.create');
});

require __DIR__.'/auth.php';
