<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_number',
        'flight_date',
        'aircraft_id',
        'airport_id',
        'mtow_used',
        'fee_amount',
        'currency_id',
        'payment_status_id',
        'payment_date',
        'receipt_reference',
        'invoice_reference',
        'authority_document',
        'notes',
        'is_calculated_auto',
        'manual_override_amount',
        'override_reason',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'flight_date' => 'date',
        'payment_date' => 'date',
        'mtow_used' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'manual_override_amount' => 'decimal:2',
        'is_calculated_auto' => 'boolean'
    ];

    public function aircraft(): BelongsTo
    {
        return $this->belongsTo(Aircraft::class);
    }

    public function airport(): BelongsTo
    {
        return $this->belongsTo(Airport::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function paymentStatus(): BelongsTo
    {
        return $this->belongsTo(PaymentStatus::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
