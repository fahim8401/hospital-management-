@extends('install.layout')

@section('title', 'Database Configuration')

@section('content')
<div class="px-8 py-7 border-b border-slate-100">
    <h2 class="text-xl font-bold text-blue-900">Step 2 – Database Configuration</h2>
    <p class="text-slate-500 text-sm mt-1">Enter your database connection details. The connection will be tested before saving.</p>
</div>

<form method="POST" action="{{ route('install.database.save') }}" class="px-8 py-6 space-y-5">
    @csrf

    {{-- Connection type --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Connection Driver</label>
        <select name="db_connection" id="db_connection"
                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                onchange="toggleDbFields(this.value)">
            <option value="mysql"  {{ old('db_connection', 'mysql') === 'mysql'  ? 'selected' : '' }}>MySQL / MariaDB</option>
            <option value="pgsql"  {{ old('db_connection') === 'pgsql'  ? 'selected' : '' }}>PostgreSQL</option>
            <option value="sqlite" {{ old('db_connection') === 'sqlite' ? 'selected' : '' }}>SQLite</option>
        </select>
    </div>

    {{-- Host / Port / Username / Password --}}
    <div id="server_fields" class="space-y-4">
        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Host</label>
                <input type="text" name="db_host" value="{{ old('db_host', '127.0.0.1') }}"
                       class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="127.0.0.1">
                @error('db_host')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Port</label>
                <input type="number" name="db_port" value="{{ old('db_port', 3306) }}"
                       class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="3306">
                @error('db_port')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
            <input type="text" name="db_username" value="{{ old('db_username', 'root') }}"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="root">
            @error('db_username')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input type="password" name="db_password" value="{{ old('db_password') }}"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Leave blank if none">
            @error('db_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    {{-- Database name --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Database Name / Path</label>
        <input type="text" name="db_database" value="{{ old('db_database', 'hms') }}"
               class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="hms  (or path for SQLite e.g. database/database.sqlite)">
        @error('db_database')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    @error('db_connection')
        <p class="text-red-600 text-sm bg-red-50 border border-red-200 rounded-lg px-4 py-2">{{ $message }}</p>
    @enderror

    <div class="pt-2 flex justify-between items-center border-t border-slate-100">
        <a href="{{ route('install.requirements') }}" class="text-sm text-slate-500 hover:text-slate-700">← Back</a>
        <button type="submit"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition">
            Test &amp; Continue
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
</form>

<script>
function toggleDbFields(driver) {
    const serverFields = document.getElementById('server_fields');
    if (driver === 'sqlite') {
        serverFields.style.display = 'none';
    } else {
        serverFields.style.display = 'block';
        // Update default port
        const portInput = document.querySelector('[name=db_port]');
        if (portInput) portInput.value = driver === 'pgsql' ? 5432 : 3306;
    }
}
// Run on page load to handle old() values
document.addEventListener('DOMContentLoaded', function () {
    toggleDbFields(document.getElementById('db_connection').value);
});
</script>
@endsection
