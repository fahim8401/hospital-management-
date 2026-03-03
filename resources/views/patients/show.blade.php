@extends('layouts.app')

@section('title', $patient->name)
@section('page-title', 'Patient Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-6">
        <a href="{{ route('patients.index') }}" class="hover:text-blue-700 transition">Patients</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-700 font-medium">{{ $patient->name }}</span>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <span class="font-mono text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded">{{ $patient->patient_id }}</span>
                <h2 class="text-2xl font-bold text-blue-900 mt-2">{{ $patient->name }}</h2>
            </div>
            <a href="{{ route('patients.edit', $patient) }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 rounded-xl transition">Edit</a>
        </div>

        <dl class="grid grid-cols-2 md:grid-cols-3 gap-5">
            <div>
                <dt class="text-xs text-slate-400 font-medium uppercase tracking-wide">Phone</dt>
                <dd class="text-sm text-slate-800 font-medium mt-0.5">{{ $patient->phone }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-400 font-medium uppercase tracking-wide">Date of Birth</dt>
                <dd class="text-sm text-slate-800 font-medium mt-0.5">{{ $patient->dob->format('d M Y') }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-400 font-medium uppercase tracking-wide">Blood Group</dt>
                <dd class="mt-0.5"><span class="inline-block bg-red-50 text-red-700 text-xs font-semibold px-2 py-0.5 rounded">{{ $patient->blood_group }}</span></dd>
            </div>
            <div>
                <dt class="text-xs text-slate-400 font-medium uppercase tracking-wide">Gender</dt>
                <dd class="text-sm text-slate-800 font-medium mt-0.5 capitalize">{{ $patient->gender }}</dd>
            </div>
            @if ($patient->nid_number)
            <div>
                <dt class="text-xs text-slate-400 font-medium uppercase tracking-wide">NID Number</dt>
                <dd class="text-sm text-slate-800 font-medium mt-0.5">{{ $patient->nid_number }}</dd>
            </div>
            @endif
            <div>
                <dt class="text-xs text-slate-400 font-medium uppercase tracking-wide">Address</dt>
                <dd class="text-sm text-slate-800 font-medium mt-0.5">{{ $patient->address }}</dd>
            </div>
        </dl>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <h3 class="text-base font-bold text-blue-900 mb-4">Appointments</h3>
        @forelse ($patient->appointments as $appt)
            <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0">
                <div>
                    <p class="text-sm font-medium text-slate-800">Dr. {{ $appt->doctor->user->name }}</p>
                    <p class="text-xs text-slate-400">{{ $appt->appointment_date->format('d M Y, h:i A') }} — Serial #{{ $appt->serial_number }}</p>
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded capitalize
                    {{ $appt->status === 'confirmed' ? 'bg-green-50 text-green-700' : ($appt->status === 'cancelled' ? 'bg-red-50 text-red-700' : 'bg-yellow-50 text-yellow-700') }}">
                    {{ $appt->status }}
                </span>
            </div>
        @empty
            <p class="text-sm text-slate-400">No appointments yet.</p>
        @endforelse
    </div>
</div>
@endsection
