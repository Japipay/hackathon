<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz and Flashcard AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 10;
        }

        .main-content {
            margin-top: 64px; /* Adjust based on navbar height */
            flex: 1;
            overflow-y: auto;
        }
    </style>
</head>

<body class="bg-gray-900 text-white">

    <!-- Navbar -->
    <nav class="bg-gray-800 p-4 flex justify-between items-center">
        <div class="flex items-center">
            <img src="https://storage.googleapis.com/a1aa/image/ELnvnfojNso03UvtKm-_qet0pBNYZO47tISvqDgV-Jo.jpg" alt="Logo" class="h-8 w-8">
        </div>

        <div class="flex space-x-4">
            <!-- Home Link (active class based on current route) -->
            <a href="{{ route('dashboard') }}"
               class="px-3 py-2 rounded-md text-sm font-medium 
               {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:text-white' }}">
                Home
            </a>

            <a href="{{ route('upload.form') }}"
               class="px-3 py-2 rounded-md text-sm font-medium 
               {{ request()->routeIs('upload.form') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:text-white' }}">
                Summarize AI
            </a>

            <a href="{{ route('flashcard.upload') }}"
               class="px-3 py-2 rounded-md text-sm font-medium 
               {{ request()->routeIs('flashcard.upload') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:text-white' }}">
                Flashcard AI
            </a>
        </div>

        <!-- Profile Dropdown -->
        <div class="relative">
            <button id="profileDropdown" class="text-white focus:outline-none">
                <i class="fas fa-user"></i>
            </button>

            <div id="dropdownMenu" class="hidden absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-lg z-50">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-700">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content container mx-auto p-4">

        <!-- Recent Quizzes -->
        {{--
        <div class="mb-8">
            <h2 class="text-2xl font-semibold mb-4">Recent Quizzes</h2>
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-semibold mb-2">Create your first quiz</h3>
                <p class="text-gray-400 mb-4">Enter your summary and get a personal quiz based on your PDF, text, or document.</p>
                <a href="{{ route('quiz.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md">Create free quiz</a>
            </div>
        </div> 
        --}}

        <!-- Recent Summaries -->
        <div class="mb-8">
    <h2 class="text-2xl font-semibold mb-4">Recent Summaries</h2>

    @if($summaries->isEmpty())
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
            <h3 class="text-xl font-semibold mb-2">No summaries available</h3>
            <p class="text-gray-400">Start uploading PDFs to generate summaries!</p>
        </div>
    @else
        @foreach($summaries as $summary)
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg mb-4">
                <h3 class="text-xl font-semibold mb-2">{{ $summary->upload->title }}</h3>
                <p class="text-gray-400 mb-4">{{ \Illuminate\Support\Str::limit($summary->summary_text, 200) }}</p>
                <a href="{{ route('summary.view', $summary->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md">View Full Summary</a>
            </div>
        @endforeach
    @endif
</div>

<!-- Recent Flashcard Sets -->
<div class="main-content container mx-auto p-4">
    <h2 class="text-2xl font-semibold mb-4">Recent Flashcard Sets</h2>

    @if ($flashcards->isEmpty())
        <p class="text-gray-400">No flashcards available. Start uploading PDFs to generate flashcards!</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($flashcards as $timestamp => $groupedFlashcards)
                <div class="bg-gray-800 p-4 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold">{{ $timestamp }}</h3>
                    <!-- <a href="{{ route('flashcards.view', $timestamp) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md">View Full Summary</a> -->
                    <ul>
                            <li class="mb-2">
                                <a href="{{ route('flashcards.view', $groupedFlashcards[0]->created_at) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md">View Full Flashcard</a>
                            </li>
                    </ul>
                </div>
            @endforeach
        </div>
    @endif
</div>

    <!-- Dropdown Toggle Script -->
    <script>
        const dropdown = document.getElementById('profileDropdown');
        const menu = document.getElementById('dropdownMenu');

        dropdown.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>

</body>

</html>
