@extends('layouts.app')

@section('title', 'Appointment Details')
@section('page-title', 'Appointment Details')

@section('content')
<div class="max-w-2xl mx-auto">
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-6">
        <a href="{{ route('appointments.index') }}" class="hover:text-blue-700 transition">Appointments</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-700 font-medium">Appointment #{{ $appointment->serial_number }}</span>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <div class="flex items-start justify-between mb-6">
            <div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded capitalize
                    {{ $appointment->status === 'confirmed' ? 'bg-green-50 text-green-700' : ($appointment->status === 'cancelled' ? 'bg-red-50 text-red-700' : 'bg-yellow-50 text-yellow-700') }}">
                    {{ $appointment->status }}
                </span>
                <h2 class="text-2xl font-bold text-blue-900 mt-2">Serial #{{ $appointment->serial_number }}</h2>
            </div>

            @if ($appointment->status === 'pending')
                <form action="{{ route('appointments.confirm', $appointment) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-xl transition">
                        Confirm Appointment
                    </button>
                </form>
            @endif
        </div>

        <dl class="grid grid-cols-2 gap-5">
            <div>
                <dt class="text-xs text-slate-400 font-medium uppercase tracking-wide">Patient</dt>
                <dd class="text-sm text-slate-800 font-medium mt-0.5">
                    <a href="{{ route('patients.show', $appointment->patient) }}" class="text-blue-700 hover:underline">{{ $appointment->patient->name }}</a>
                </dd>
            </div>
            <div>
                <dt class="text-xs text-slate-400 font-medium uppercase tracking-wide">Doctor</dt>
                <dd class="text-sm text-slate-800 font-medium mt-0.5">Dr. {{ $appointment->doctor->user->name }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-400 font-medium uppercase tracking-wide">Department</dt>
                <dd class="text-sm text-slate-800 font-medium mt-0.5">{{ $appointment->doctor->department->name }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-400 font-medium uppercase tracking-wide">Date & Time</dt>
                <dd class="text-sm text-slate-800 font-medium mt-0.5">{{ $appointment->appointment_date->format('d M Y, h:i A') }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-400 font-medium uppercase tracking-wide">Payment Status</dt>
                <dd class="mt-0.5">
                    <span class="text-xs font-semibold px-2 py-0.5 rounded {{ $appointment->payment_status === 'paid' ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ $appointment->payment_status }}
                    </span>
                </dd>
            </div>
            @if ($appointment->notes)
            <div class="col-span-2">
                <dt class="text-xs text-slate-400 font-medium uppercase tracking-wide">Notes</dt>
                <dd class="text-sm text-slate-800 mt-0.5">{{ $appointment->notes }}</dd>
            </div>
            @endif
        </dl>

        @if ($appointment->status !== 'cancelled' && $appointment->status !== 'completed')
            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end">
                <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" onsubmit="return confirm('Cancel this appointment?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition">
                        Cancel Appointment
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
