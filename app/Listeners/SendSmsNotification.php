<?php

namespace App\Listeners;

use App\Events\AppointmentConfirmed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendSmsNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(AppointmentConfirmed $event): void
    {
        $appointment = $event->appointment->load(['patient', 'doctor.user']);

        $phone        = $appointment->patient->phone;
        $doctorName   = $appointment->doctor->user->name;
        $date         = $appointment->appointment_date->format('d M Y, h:i A');
        $serialNumber = $appointment->serial_number;

        $message = "Your appointment with Dr. {$doctorName} is confirmed on {$date}. Serial: {$serialNumber}. BDHealthSync";

        $apiUrl  = config('services.sms.url', 'https://api.smsq.com.bd/api/v2/SendSMS');
        $apiKey  = config('services.sms.api_key', '');
        $senderId = config('services.sms.sender_id', 'BDHealthSync');

        try {
            $response = Http::timeout(10)->post($apiUrl, [
                'api_key'   => $apiKey,
                'sender_id' => $senderId,
                'message'   => $message,
                'number'    => '880' . ltrim($phone, '0'),
            ]);

            if (! $response->successful()) {
                Log::error('SMS gateway error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                    'phone'  => $phone,
                ]);
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('SMS gateway connection timeout', [
                'phone'   => $phone,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(AppointmentConfirmed $event, \Throwable $exception): void
    {
        Log::error('SendSmsNotification listener failed', [
            'appointment_id' => $event->appointment->id,
            'error'          => $exception->getMessage(),
        ]);
    }
}
