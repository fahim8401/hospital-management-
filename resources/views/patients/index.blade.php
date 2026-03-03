@extends('layouts.app')

@section('title', 'Patients')
@section('page-title', 'Patient Management')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-slate-500">{{ $patients->total() }} total patients registered</p>
    <a href="{{ route('patients.create') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-blue-700 hover:bg-blue-800 rounded-xl shadow-sm transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Register Patient
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
                <th class="text-left px-5 py-3.5 font-semibold text-slate-600">Patient ID</th>
                <th class="text-left px-5 py-3.5 font-semibold text-slate-600">Name</th>
                <th class="text-left px-5 py-3.5 font-semibold text-slate-600">Phone</th>
                <th class="text-left px-5 py-3.5 font-semibold text-slate-600">Blood Group</th>
                <th class="text-left px-5 py-3.5 font-semibold text-slate-600">Gender</th>
                <th class="text-left px-5 py-3.5 font-semibold text-slate-600">Registered</th>
                <th class="px-5 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($patients as $patient)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-5 py-3.5">
                        <span class="font-mono text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded">{{ $patient->patient_id }}</span>
                    </td>
                    <td class="px-5 py-3.5 font-medium text-slate-800">{{ $patient->name }}</td>
                    <td class="px-5 py-3.5 text-slate-600">{{ $patient->phone }}</td>
                    <td class="px-5 py-3.5">
                        <span class="inline-block bg-red-50 text-red-700 text-xs font-semibold px-2 py-0.5 rounded">{{ $patient->blood_group }}</span>
                    </td>
                    <td class="px-5 py-3.5 text-slate-600 capitalize">{{ $patient->gender }}</td>
                    <td class="px-5 py-3.5 text-slate-400 text-xs">{{ $patient->created_at->format('d M Y') }}</td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2 justify-end">
                            <a href="{{ route('patients.show', $patient) }}" class="px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition">View</a>
                            <a href="{{ route('patients.edit', $patient) }}" class="px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition">Edit</a>
                            <form method="POST" action="{{ route('patients.destroy', $patient) }}" onsubmit="return confirm('Remove this patient?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center text-slate-400">
                        <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-4a4 4 0 10-8 0 4 4 0 008 0z"/>
                        </svg>
                        No patients registered yet.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($patients->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $patients->links() }}
        </div>
    @endif
</div>
@endsection
