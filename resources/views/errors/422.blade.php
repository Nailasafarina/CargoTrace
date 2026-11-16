<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>422 Unprocessable Entity - CargoTrace</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-6 bg-slate-950 text-slate-100">
    <div class="max-w-md w-full p-8 rounded-2xl bg-slate-900 border border-amber-500/30 text-center shadow-2xl">
        <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-400 mx-auto flex items-center justify-center mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold uppercase bg-amber-500/10 text-amber-400 border border-amber-500/30">
            HTTP 422: Unprocessable Entity
        </span>
        <h1 class="text-xl font-bold text-white mt-3">Invalid State or Timeline Rule</h1>
        <p class="text-xs text-slate-400 mt-2 leading-relaxed">
            {{ $exception?->getMessage() ?? 'The requested operation violates the shipment status lifecycle state machine or timestamp ordering rules.' }}
        </p>
        <div class="mt-6">
            <a href="javascript:history.back()" class="inline-block px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-medium text-slate-300 transition">
                &larr; Return to Previous Page
            </a>
        </div>
    </div>
</body>
</html>
