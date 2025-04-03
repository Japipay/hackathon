<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generated Flashcards - NoteAlly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        .flashcard {
            perspective: 1000px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .flashcard-inner {
            transition: transform 0.6s ease-in-out; /* Smooth animation */
            transform-style: preserve-3d;
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .flashcard.flipped .flashcard-inner {
            transform: rotateY(180deg); /* Flip animation */
        }
        .flashcard-front, .flashcard-back {
            backface-visibility: hidden;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            padding: 2rem; /* Increased padding */
            text-align: center;
            font-size: 1.5rem; /* Increased font size */
        }
        .flashcard-front {
            background-color: #1f2937;
            color: #ffffff;
        }
        .flashcard-back {
            background-color: #374151;
            color: #ffffff;
            transform: rotateY(180deg);
        }
    </style>
</head>

<body class="min-h-screen bg-gray-900 text-white flex flex-col items-center">
    <div class="w-full max-w-5xl px-6 mt-12">
        <h1 class="text-4xl font-bold text-center mb-8"><i class="fas fa-magic text-blue-500"></i> Generated Flashcards</h1>

        <div id="flashcard-container" class="flex justify-center items-center">
            @foreach($flashcards as $index => $flashcard)
                <div class="flashcard w-[30rem] h-[20rem] bg-gray-800 p-6 rounded-lg shadow-lg {{ $index === 0 ? '' : 'hidden' }}" data-index="{{ $index }}">
                    <div class="flashcard-inner">
                        <div class="flashcard-front">
                            <h3 class="text-2xl font-semibold">{{ $flashcard['question'] }}</h3> <!-- Increased text size -->
                        </div>
                        <div class="flashcard-back">
                            <p class="text-xl"><strong>Answer:</strong> {{ $flashcard['answer'] }}</p> <!-- Increased text size -->
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 flex justify-between">
            <a href="/upload" class="bg-gray-600 text-white px-6 py-3 rounded-md shadow hover:bg-gray-500">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
            <div class="flex space-x-4">
                <button id="prev-btn" class="bg-gray-600 text-white px-6 py-3 rounded-md shadow hover:bg-gray-500" disabled>
                    <i class="fas fa-arrow-left mr-2"></i> Previous
                </button>
                <button id="next-btn" class="bg-blue-600 text-white px-6 py-3 rounded-md shadow hover:bg-blue-500">
                    Next <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const flashcards = document.querySelectorAll('.flashcard');
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            let currentIndex = 0;

            flashcards.forEach((card, index) => {
                card.addEventListener('click', () => {
                    if (index === currentIndex) {
                        card.classList.toggle('flipped'); // Toggle flip animation
                    }
                });
            });

            function updateFlashcards() {
                flashcards.forEach((card, index) => {
                    card.classList.toggle('hidden', index !== currentIndex);
                    card.classList.remove('flipped'); // Reset flip state when navigating
                });
                prevBtn.disabled = currentIndex === 0;
                nextBtn.disabled = currentIndex === flashcards.length - 1;
            }

            prevBtn.addEventListener('click', () => {
                if (currentIndex > 0) {
                    currentIndex--;
                    updateFlashcards();
                }
            });

            nextBtn.addEventListener('click', () => {
                if (currentIndex < flashcards.length - 1) {
                    currentIndex++;
                    updateFlashcards();
                }
            });

            updateFlashcards();
        });
    </script>
</body>

</html>
