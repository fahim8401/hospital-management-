@extends('layouts.app')

@section('title', 'Book Appointment')
@section('page-title', 'Book Appointment')

@section('content')
<div class="max-w-2xl mx-auto">
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-6">
        <a href="{{ route('appointments.index') }}" class="hover:text-blue-700 transition">Appointments</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-700 font-medium">Book Appointment</span>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-800 to-blue-600 px-8 py-6">
            <h2 class="text-xl font-bold text-white">New Appointment</h2>
            <p class="text-blue-200 text-sm mt-0.5">Fill in the appointment details below</p>
        </div>

        <form action="{{ route('appointments.store') }}" method="POST" class="px-8 py-8 space-y-6">
            @csrf

            <div>
                <label for="patient_id" class="block text-sm font-semibold text-slate-700 mb-1.5">Patient <span class="text-red-500">*</span></label>
                <select id="patient_id" name="patient_id"
                    class="w-full px-4 py-2.5 border {{ $errors->has('patient_id') ? 'border-red-400 bg-red-50' : 'border-slate-300' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
                    <option value="">— Select Patient —</option>
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                            {{ $patient->name }} ({{ $patient->patient_id }})
                        </option>
                    @endforeach
                </select>
                @error('patient_id')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="doctor_id" class="block text-sm font-semibold text-slate-700 mb-1.5">Doctor <span class="text-red-500">*</span></label>
                <select id="doctor_id" name="doctor_id"
                    class="w-full px-4 py-2.5 border {{ $errors->has('doctor_id') ? 'border-red-400 bg-red-50' : 'border-slate-300' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
                    <option value="">— Select Doctor —</option>
                    @foreach ($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                            Dr. {{ $doctor->user->name }} — {{ $doctor->specialization }} ({{ $doctor->department->name }})
                        </option>
                    @endforeach
                </select>
                @error('doctor_id')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="appointment_date" class="block text-sm font-semibold text-slate-700 mb-1.5">Appointment Date & Time <span class="text-red-500">*</span></label>
                <input type="datetime-local" id="appointment_date" name="appointment_date"
                    value="{{ old('appointment_date') }}"
                    min="{{ now()->addMinutes(30)->format('Y-m-d\TH:i') }}"
                    class="w-full px-4 py-2.5 border {{ $errors->has('appointment_date') ? 'border-red-400 bg-red-50' : 'border-slate-300' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                @error('appointment_date')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="notes" class="block text-sm font-semibold text-slate-700 mb-1.5">Notes <span class="text-slate-400 font-normal">(optional)</span></label>
                <textarea id="notes" name="notes" rows="3"
                    placeholder="Any additional notes or symptoms..."
                    class="w-full px-4 py-2.5 border {{ $errors->has('notes') ? 'border-red-400 bg-red-50' : 'border-slate-300' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                >{{ old('notes') }}</textarea>
                @error('notes')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('appointments.index') }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">Cancel</a>
                <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-blue-700 hover:bg-blue-800 rounded-xl shadow-sm transition">
                    Book Appointment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
