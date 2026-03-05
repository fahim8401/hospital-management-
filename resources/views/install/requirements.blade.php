@extends('install.layout')

@section('title', 'Requirements Check')

@section('content')
<div class="px-8 py-7 border-b border-slate-100">
    <h2 class="text-xl font-bold text-blue-900">Step 1 – Requirements Check</h2>
    <p class="text-slate-500 text-sm mt-1">Make sure your server meets all requirements before continuing.</p>
</div>

<div class="px-8 py-6 space-y-3">
    @foreach ($requirements as $req)
        <div class="flex items-center justify-between py-2.5 px-4 rounded-lg {{ $req['status'] ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
            <span class="text-sm font-medium {{ $req['status'] ? 'text-green-800' : 'text-red-800' }}">
                {{ $req['name'] }}
            </span>
            <span class="flex items-center gap-1.5 text-xs font-semibold {{ $req['status'] ? 'text-green-700' : 'text-red-700' }}">
                @if ($req['status'])
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                @endif
                {{ $req['current'] }}
            </span>
        </div>
    @endforeach
</div>

<div class="px-8 py-5 bg-slate-50 border-t border-slate-100 flex justify-end">
    @if ($allPassed)
        <a href="{{ route('install.database') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition">
            Continue
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    @else
        <p class="text-sm text-red-600 font-medium">Please resolve all failing requirements before continuing.</p>
    @endif
</div>
@endsection
