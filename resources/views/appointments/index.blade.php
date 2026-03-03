@extends('layouts.app')

@section('title', 'Appointments')
@section('page-title', 'Appointment Management')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-slate-500">{{ $appointments->total() }} total appointments</p>
    <a href="{{ route('appointments.create') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-blue-700 hover:bg-blue-800 rounded-xl shadow-sm transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Book Appointment
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
                <th class="text-left px-5 py-3.5 font-semibold text-slate-600">Serial</th>
                <th class="text-left px-5 py-3.5 font-semibold text-slate-600">Patient</th>
                <th class="text-left px-5 py-3.5 font-semibold text-slate-600">Doctor</th>
                <th class="text-left px-5 py-3.5 font-semibold text-slate-600">Date & Time</th>
                <th class="text-left px-5 py-3.5 font-semibold text-slate-600">Status</th>
                <th class="text-left px-5 py-3.5 font-semibold text-slate-600">Payment</th>
                <th class="px-5 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($appointments as $appt)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-5 py-3.5 font-mono text-xs text-slate-500">#{{ $appt->serial_number }}</td>
                    <td class="px-5 py-3.5 font-medium text-slate-800">{{ $appt->patient->name }}</td>
                    <td class="px-5 py-3.5 text-slate-600">Dr. {{ $appt->doctor->user->name }}</td>
                    <td class="px-5 py-3.5 text-slate-600">{{ $appt->appointment_date->format('d M Y, h:i A') }}</td>
                    <td class="px-5 py-3.5">
                        <span class="text-xs font-semibold px-2 py-0.5 rounded capitalize
                            {{ $appt->status === 'confirmed' ? 'bg-green-50 text-green-700' : ($appt->status === 'cancelled' ? 'bg-red-50 text-red-700' : ($appt->status === 'completed' ? 'bg-blue-50 text-blue-700' : 'bg-yellow-50 text-yellow-700')) }}">
                            {{ $appt->status }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-xs font-semibold px-2 py-0.5 rounded {{ $appt->payment_status === 'paid' ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $appt->payment_status }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <a href="{{ route('appointments.show', $appt) }}" class="px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center text-slate-400">
                        No appointments found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($appointments->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $appointments->links() }}
        </div>
    @endif
</div>
@endsection
