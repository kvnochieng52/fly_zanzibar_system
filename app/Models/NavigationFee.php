<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NavigationFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_number',
        'flight_date',
        'aircraft_id',
        'route_from',
        'route_to',
        'route_waypoints',
        'distance_km',
        'aircraft_mtow',
        'fee_amount',
        'currency_id',
        'service_provider_id',
        'payment_status_id',
        'payment_date',
        'invoice_reference',
        'provider_document',
        'billing_details',
        'notes',
        'is_calculated_auto',
        'rate_per_km',
        'manual_override_amount',
        'override_reason',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'flight_date' => 'date',
        'payment_date' => 'date',
        'distance_km' => 'decimal:2',
        'aircraft_mtow' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'rate_per_km' => 'decimal:4',
        'manual_override_amount' => 'decimal:2',
        'is_calculated_auto' => 'boolean'
    ];

    public function aircraft(): BelongsTo
    {
        return $this->belongsTo(Aircraft::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function serviceProvider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class);
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
