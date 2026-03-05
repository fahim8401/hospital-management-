<?php

namespace App\Http\Controllers;

use App\Events\AppointmentConfirmed;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(): View
    {
        $appointments = Appointment::with(['patient', 'doctor.user'])
            ->latest()
            ->paginate(15);

        return view('appointments.index', compact('appointments'));
    }

    public function create(): View
    {
        $patients = Patient::orderBy('name')->get();
        $doctors  = Doctor::with(['user', 'department'])
            ->where('availability_status', 'available')
            ->get();

        return view('appointments.create', compact('patients', 'doctors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id'       => ['required', 'exists:patients,id'],
            'doctor_id'        => ['required', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date_format:Y-m-d H:i', 'after:now'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ]);

        $appointmentDate = $validated['appointment_date'];
        $doctorId        = $validated['doctor_id'];

        // Prevent double-booking: same doctor, same exact datetime
        $conflict = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $appointmentDate)
            ->whereNotIn('status', ['cancelled'])
            ->exists();

        if ($conflict) {
            return back()
                ->withInput()
                ->withErrors(['appointment_date' => 'The doctor already has an appointment at this date and time. Please choose a different slot.']);
        }

        // Assign the next serial number for this doctor on the given day
        $dateOnly = date('Y-m-d', strtotime($appointmentDate));

        $lastSerial = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $dateOnly)
            ->whereNotIn('status', ['cancelled'])
            ->max('serial_number');

        $validated['serial_number'] = ($lastSerial ?? 0) + 1;
        $validated['status']        = 'pending';
        $validated['payment_status'] = 'unpaid';

        $appointment = Appointment::create($validated);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', "Appointment booked. Serial #{$appointment->serial_number}.");
    }

    public function show(Appointment $appointment): View
    {
        $appointment->load(['patient', 'doctor.user', 'doctor.department']);

        return view('appointments.show', compact('appointment'));
    }

    public function confirm(Appointment $appointment): RedirectResponse
    {
        $appointment->update(['status' => 'confirmed']);

        event(new AppointmentConfirmed($appointment));

        return back()->with('success', 'Appointment confirmed and SMS notification queued.');
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        $appointment->update(['status' => 'cancelled']);

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment cancelled.');
    }
}
