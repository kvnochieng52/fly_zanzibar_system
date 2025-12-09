<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number',
        'date',
        'invoice_id',
        'customer_id',
        'payment_amount',
        'payment_method_id',
        'transaction_reference',
        'received_by',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'payment_amount' => 'decimal:2'
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted()
    {
        static::creating(function ($receipt) {
            if (empty($receipt->receipt_number)) {
                $receipt->receipt_number = static::generateReceiptNumber();
            }
        });
    }

    public static function generateReceiptNumber(): string
    {
        $year = date('Y');
        $month = date('m');

        $lastReceipt = static::whereYear('created_at', $year)
                           ->whereMonth('created_at', $month)
                           ->orderBy('id', 'desc')
                           ->first();

        $nextNumber = $lastReceipt ?
            intval(substr($lastReceipt->receipt_number, -4)) + 1 : 1;

        return 'RCT-' . $year . $month . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
