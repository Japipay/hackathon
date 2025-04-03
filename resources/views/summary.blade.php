<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Generated Summary</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-900 text-white min-h-screen flex flex-col items-center">

    <!-- AI Generated Summary -->
    <div class="w-full max-w-5xl mt-12 px-6">
        <h2 class="text-4xl font-bold text-center mb-8"><i class="fas fa-lightbulb text-yellow-400 mr-2"></i> AI Generated Summary</h2>
        <div class="bg-gray-800 p-8 rounded-lg shadow-lg">
            <div class="p-6 bg-gray-700 rounded-lg text-white max-h-[500px] overflow-y-auto border border-gray-600">
                <pre style="white-space: pre-wrap;" class="text-base leading-relaxed">{{ $summary }}</pre>
            </div>
            <div class="mt-8 flex justify-between">
                <a href="{{ route('upload.form') }}" class="bg-gray-600 text-white px-6 py-3 rounded-md shadow hover:bg-gray-500">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Upload
                </a>
                <button onclick="copySummary()" class="bg-blue-600 text-white px-6 py-3 rounded-md shadow hover:bg-blue-500">
                    <i class="fas fa-copy mr-2"></i> Copy Summary
                </button>
            </div>
        </div>
    </div>

    <script>
        function copySummary() {
            const summary = document.querySelector('pre').innerText;
            navigator.clipboard.writeText(summary)
                .then(() => {
                    alert('Summary copied to clipboard!');
                })
                .catch(err => {
                    alert('Failed to copy: ' + err);
                });
        }
    </script>
</body>

</html>