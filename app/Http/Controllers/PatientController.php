<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(): View
    {
        $patients = Patient::latest()->paginate(15);

        return view('patients.index', compact('patients'));
    }

    public function create(): View
    {
        return view('patients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'phone'       => ['required', 'string', 'regex:/^01[3-9]\d{8}$/'],
            'nid_number'  => ['nullable', 'string', 'max:20'],
            'dob'         => ['required', 'date', 'before:today'],
            'blood_group' => ['required', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'gender'      => ['required', 'in:male,female,other'],
            'address'     => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($validated) {
            Patient::create($validated);
        });

        return redirect()->route('patients.index')
            ->with('success', 'Patient registered successfully.');
    }

    public function show(Patient $patient): View
    {
        $patient->load(['appointments.doctor.user', 'invoices']);

        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient): View
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'phone'       => ['required', 'string', 'regex:/^01[3-9]\d{8}$/'],
            'nid_number'  => ['nullable', 'string', 'max:20'],
            'dob'         => ['required', 'date', 'before:today'],
            'blood_group' => ['required', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'gender'      => ['required', 'in:male,female,other'],
            'address'     => ['required', 'string', 'max:500'],
        ]);

        $patient->update($validated);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Patient removed successfully.');
    }
}
