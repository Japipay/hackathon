<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NoteAlly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> <!-- Include Bootstrap JS -->
    <style>
        body {
            padding-top: 64px; /* Adjust for fixed navbar */
        }
        .main-container {
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            background-color: #1f2937;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn {
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            filter: brightness(1.1);
        }
    </style>
</head>

<body class="bg-gray-900 text-white">
    <!-- Navbar -->
    <nav class="bg-gray-800 p-4 fixed top-0 left-0 right-0 z-50 flex justify-between items-center">
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
    <div class="main-container w-full max-w-4xl">
        <h2 class="text-3xl font-bold mb-6 text-center"><i class="fas fa-file-pdf text-red-500"></i> PDF Summarizer</h2>

        @if(session('error'))
            <div class="bg-red-600 text-white p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="card mb-6">
            <h3 class="text-lg font-semibold mb-4 text-center">Upload PDF File</h3>
            <form action="{{ route('upload.summarize') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 text-center">Drag & Drop or Click to Upload PDF (Max 10MB)</label>
                    <div id="dropzone" class="w-full p-6 border-2 border-dashed border-blue-500 rounded bg-gray-700 text-center text-gray-300 cursor-pointer">
                        <i class="fas fa-folder-open text-blue-500 text-4xl mb-2"></i>
                        <p id="dropzone-text">Drop your PDF here or click to browse</p>
                        <input class="hidden" type="file" id="pdfFile" name="pdf" accept=".pdf" required>
                    </div>
                    @error('pdf')
                        <div class="text-red-400 mt-1 text-center">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </div>

        <div class="flex gap-4 mb-6">
            <button type="submit" form="uploadForm" class="btn bg-blue-600 text-white px-4 py-2 rounded w-full shadow">
                <i class="fas fa-align-left mr-2"></i> Generate Summary
            </button>
        </div>

        <div class="card mt-6">
            <h5 class="text-lg font-semibold text-gray-300 mb-4 text-center"><i class="fas fa-lightbulb text-yellow-400 mr-2"></i> How it works:</h5>
            <ol class="list-decimal pl-6 text-gray-400 space-y-2">
                <li>Upload any PDF document (reports, articles, etc.)</li>
                <li>Click "Generate Summary" to get an AI-processed summary</li>
            </ol>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pdfFileInput = document.getElementById('pdfFile');
            const dropzone = document.getElementById('dropzone');
            
            // Store the file when selected
            let selectedFile = null;

            pdfFileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    selectedFile = this.files[0];
                    document.getElementById('dropzone-text').textContent = selectedFile.name;
                }
            });

            dropzone.addEventListener('click', function() {
                pdfFileInput.click();
            });

            dropzone.addEventListener('dragover', function(e) {
                e.preventDefault();
                dropzone.classList.add('border-blue-700');
            });

            dropzone.addEventListener('dragleave', function() {
                dropzone.classList.remove('border-blue-700');
            });

            dropzone.addEventListener('drop', function(e) {
                e.preventDefault();
                dropzone.classList.remove('border-blue-700');
                if (e.dataTransfer.files.length > 0) {
                    selectedFile = e.dataTransfer.files[0];
                    pdfFileInput.files = e.dataTransfer.files;
                    document.getElementById('dropzone-text').textContent = selectedFile.name;
                }
            });
        });
    </script>
</body>

</html>