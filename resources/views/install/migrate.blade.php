@extends('install.layout')

@section('title', 'Run Migrations')

@section('content')
<div class="px-8 py-7 border-b border-slate-100">
    <h2 class="text-xl font-bold text-blue-900">Step 4 – Run Database Migrations</h2>
    <p class="text-slate-500 text-sm mt-1">
        This will write your <code class="bg-slate-100 px-1 rounded text-xs">.env</code> file, generate an application key,
        and run all database migrations.
    </p>
</div>

<div class="px-8 py-6 space-y-4">
    <ul class="space-y-2 text-sm text-slate-600">
        <li class="flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Write <strong>.env</strong> with your database &amp; application settings
        </li>
        <li class="flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Generate a secure <strong>APP_KEY</strong>
        </li>
        <li class="flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Run all <strong>database migrations</strong> to create the tables
        </li>
    </ul>

    <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 text-sm text-amber-800">
        <strong>Note:</strong> If a <code>.env</code> file already exists it will be overwritten with your new settings.
    </div>
</div>

<form method="POST" action="{{ route('install.migrate.run') }}">
    @csrf
    <div class="px-8 py-5 bg-slate-50 border-t border-slate-100 flex justify-between items-center">
        <a href="{{ route('install.app-config') }}" class="text-sm text-slate-500 hover:text-slate-700">← Back</a>
        <button type="submit"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition">
            Run Migrations
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
</form>
@endsection
