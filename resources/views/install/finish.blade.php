@extends('install.layout')

@section('title', 'Installation Complete')

@section('content')
<div class="px-8 py-10 text-center">
    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-5">
        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h2 class="text-2xl font-bold text-blue-900 mb-2">Installation Complete!</h2>
    <p class="text-slate-500 text-sm mb-6">
        BDHealthSync HMS has been successfully installed and configured.<br>
        You can now log in with the administrator account you just created.
    </p>

    <div class="bg-blue-50 border border-blue-200 rounded-xl px-6 py-4 text-left space-y-2 text-sm text-blue-800 mb-8 max-w-sm mx-auto">
        <p class="font-semibold text-blue-900">What was done:</p>
        <ul class="space-y-1 list-disc list-inside text-blue-700">
            <li>.env file written &amp; APP_KEY generated</li>
            <li>Database tables created via migrations</li>
            <li>Administrator account created</li>
            <li>Installation lock file saved</li>
        </ul>
    </div>

    <a href="/"
       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-8 py-3 rounded-lg transition shadow-md">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h5v-6h4v6h5a1 1 0 001-1V10"/>
        </svg>
        Go to Dashboard
    </a>
</div>
@endsection
