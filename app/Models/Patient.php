<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Patient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'patient_id',
        'name',
        'phone',
        'nid_number',
        'dob',
        'blood_group',
        'gender',
        'address',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    /**
     * Boot method to auto-generate patient_id on creation.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Patient $patient) {
            if (empty($patient->patient_id)) {
                $patient->patient_id = self::generatePatientId();
            }
        });
    }

    /**
     * Generate a unique patient ID in the format PID-YYYYMMDD-XXXX.
     */
    protected static function generatePatientId(): string
    {
        $date = now()->format('Ymd');
        $suffix = strtoupper(Str::random(4));
        $candidate = "PID-{$date}-{$suffix}";

        while (self::withTrashed()->where('patient_id', $candidate)->exists()) {
            $suffix = strtoupper(Str::random(4));
            $candidate = "PID-{$date}-{$suffix}";
        }

        return $candidate;
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
