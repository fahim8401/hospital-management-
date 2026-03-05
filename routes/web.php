<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

// All main application routes are protected behind the 'installed' middleware.
// If the app has not been installed yet, visitors are redirected to /install.
Route::middleware('installed')->group(function () {

    Route::get('/', function () {
        return view('welcome');
    });

    // Patient management
    Route::resource('patients', PatientController::class);

    // Appointment management
    Route::resource('appointments', AppointmentController::class);
    Route::post('appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])
        ->name('appointments.confirm');

    // Payment callback stubs (for bKash/Nagad redirect URLs)
    Route::get('payments/bkash/callback', fn () => 'bKash callback')->name('payments.bkash.callback');
    Route::get('payments/nagad/callback', fn () => 'Nagad callback')->name('payments.nagad.callback');
});

