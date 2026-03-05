<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Installation Wizard') – BDHealthSync HMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#1e40af', light: '#3b82f6', dark: '#1e3a8a' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex flex-col">

{{-- Header --}}
<header class="bg-blue-900 text-white py-4 px-6 flex items-center gap-3 shadow-lg">
    <svg class="w-8 h-8 text-blue-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21H5a2 2 0 01-2-2V7l7-4 7 4v12a2 2 0 01-2 2z"/>
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 21V12h6v9"/>
    </svg>
    <div>
        <span class="text-xl font-bold tracking-wide">BDHealthSync HMS</span>
        <span class="ml-3 text-sm text-blue-300">Installation Wizard</span>
    </div>
</header>

{{-- Progress Steps --}}
<div class="bg-white border-b border-slate-200 shadow-sm">
    <div class="max-w-3xl mx-auto px-6 py-4">
        <ol class="flex items-center gap-1 text-xs overflow-x-auto">
            @php
                $steps = [
                    ['route' => 'install.requirements', 'label' => '1. Requirements'],
                    ['route' => 'install.database',     'label' => '2. Database'],
                    ['route' => 'install.app-config',   'label' => '3. App Config'],
                    ['route' => 'install.migrate',      'label' => '4. Migrate'],
                    ['route' => 'install.admin',        'label' => '5. Admin User'],
                    ['route' => 'install.finish',       'label' => '6. Finish'],
                ];
                $currentRoute = request()->route()->getName();
                $stepNames    = array_column($steps, 'route');
                $currentIndex = array_search($currentRoute, $stepNames, true);
                $currentIndex = $currentIndex === false ? count($steps) - 1 : $currentIndex;
            @endphp
            @foreach ($steps as $i => $step)
                <li class="flex items-center gap-1 whitespace-nowrap">
                    @if ($i < $currentIndex)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            {{ $step['label'] }}
                        </span>
                    @elseif ($i === $currentIndex)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-600 text-white font-semibold">
                            {{ $step['label'] }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-100 text-slate-400 font-medium">
                            {{ $step['label'] }}
                        </span>
                    @endif
                    @if (! $loop->last)
                        <svg class="w-4 h-4 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>
</div>

{{-- Content --}}
<main class="flex-1 flex items-start justify-center py-10 px-4">
    <div class="w-full max-w-2xl">

        {{-- Errors --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
                <p class="font-semibold mb-1">Please fix the following errors:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden">
            @yield('content')
        </div>

    </div>
</main>

<footer class="py-4 text-center text-xs text-slate-400">
    BDHealthSync HMS &mdash; Installation Wizard
</footer>

</body>
</html>
