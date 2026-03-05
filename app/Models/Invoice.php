<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'patient_id',
        'invoice_number',
        'sub_total',
        'discount',
        'tax',
        'grand_total',
        'payment_method',
        'payment_status',
        'transaction_id',
    ];

    protected $casts = [
        'sub_total'   => 'decimal:2',
        'discount'    => 'decimal:2',
        'tax'         => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Accessor: formatted grand total with Bangladeshi Taka symbol.
     */
    protected function formattedTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => '৳' . number_format((float) $this->grand_total, 2),
        );
    }
}
