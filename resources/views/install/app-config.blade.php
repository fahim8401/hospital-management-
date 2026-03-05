@extends('install.layout')

@section('title', 'Application Configuration')

@section('content')
<div class="px-8 py-7 border-b border-slate-100">
    <h2 class="text-xl font-bold text-blue-900">Step 3 – Application Configuration</h2>
    <p class="text-slate-500 text-sm mt-1">Set the application name, URL, and environment settings.</p>
</div>

<form method="POST" action="{{ route('install.app-config.save') }}" class="px-8 py-6 space-y-5">
    @csrf

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Application Name</label>
        <input type="text" name="app_name" value="{{ old('app_name', 'BDHealthSync HMS') }}"
               class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="My Hospital">
        @error('app_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Application URL</label>
        <input type="url" name="app_url" value="{{ old('app_url', request()->getSchemeAndHttpHost()) }}"
               class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="https://hms.example.com">
        @error('app_url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Environment</label>
        <select name="app_env"
                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="production" {{ old('app_env', 'production') === 'production' ? 'selected' : '' }}>Production</option>
            <option value="local"      {{ old('app_env') === 'local' ? 'selected' : '' }}>Local / Development</option>
        </select>
        @error('app_env')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-center gap-3">
        <input type="hidden" name="app_debug" value="0">
        <input type="checkbox" name="app_debug" id="app_debug" value="1"
               class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500"
               {{ old('app_debug') ? 'checked' : '' }}>
        <label for="app_debug" class="text-sm text-slate-700">Enable debug mode <span class="text-slate-400">(not recommended in production)</span></label>
    </div>

    <div class="pt-2 flex justify-between items-center border-t border-slate-100">
        <a href="{{ route('install.database') }}" class="text-sm text-slate-500 hover:text-slate-700">← Back</a>
        <button type="submit"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition">
            Continue
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
</form>
@endsection
