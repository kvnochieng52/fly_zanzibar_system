<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_code',
        'type',
        'first_name',
        'last_name',
        'title',
        'company_name',
        'contact_person',
        'tax_number',
        'registration_number',
        'email',
        'phone',
        'mobile',
        'fax',
        'address_line_1',
        'address_line_2',
        'city',
        'state_province',
        'postal_code',
        'country',
        'billing_address_line_1',
        'billing_address_line_2',
        'billing_city',
        'billing_state_province',
        'billing_postal_code',
        'billing_country',
        'payment_terms',
        'credit_limit',
        'default_currency_id',
        'is_active',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credit_limit' => 'decimal:2'
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'default_currency_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->type === 'corporate'
            ? $this->company_name
            : $this->first_name . ' ' . $this->last_name;
    }
}
