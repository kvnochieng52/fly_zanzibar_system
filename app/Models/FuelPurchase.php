<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FuelPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_number',
        'purchase_date',
        'aircraft_id',
        'airport_id',
        'fuel_supplier_id',
        'fuel_unit_id',
        'quantity',
        'unit_price',
        'total_amount',
        'currency_id',
        'invoice_reference',
        'invoice_date',
        'payment_status_id',
        'payment_date',
        'supplier_document',
        'fuel_grade',
        'density',
        'fuel_quality_rating',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'invoice_date' => 'date',
        'payment_date' => 'date',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:4',
        'total_amount' => 'decimal:2',
        'density' => 'decimal:4',
        'fuel_quality_rating' => 'decimal:2'
    ];

    public function aircraft(): BelongsTo
    {
        return $this->belongsTo(Aircraft::class);
    }

    public function airport(): BelongsTo
    {
        return $this->belongsTo(Airport::class);
    }

    public function fuelSupplier(): BelongsTo
    {
        return $this->belongsTo(FuelSupplier::class);
    }

    public function fuelUnit(): BelongsTo
    {
        return $this->belongsTo(FuelUnit::class);
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

    public function costAllocations(): HasMany
    {
        return $this->hasMany(FuelCostAllocation::class);
    }
}
