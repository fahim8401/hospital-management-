@extends('install.layout')

@section('title', 'Create Admin User')

@section('content')
<div class="px-8 py-7 border-b border-slate-100">
    <h2 class="text-xl font-bold text-blue-900">Step 5 – Create Administrator Account</h2>
    <p class="text-slate-500 text-sm mt-1">Create the first admin account that you will use to log in to the system.</p>
</div>

<form method="POST" action="{{ route('install.admin.save') }}" class="px-8 py-6 space-y-5">
    @csrf

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
        <input type="text" name="name" value="{{ old('name') }}" required
               class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="Dr. Admin">
        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
        <input type="email" name="email" value="{{ old('email') }}" required
               class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="admin@hospital.com">
        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Password <span class="text-slate-400 font-normal">(min. 8 characters)</span></label>
        <input type="password" name="password" required minlength="8"
               class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="••••••••">
        @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Confirm Password</label>
        <input type="password" name="password_confirmation" required minlength="8"
               class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="••••••••">
    </div>

    <div class="pt-2 flex justify-between items-center border-t border-slate-100">
        <a href="{{ route('install.migrate') }}" class="text-sm text-slate-500 hover:text-slate-700">← Back</a>
        <button type="submit"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition">
            Create Admin &amp; Finish
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
</form>
@endsection
