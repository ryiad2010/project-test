<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class BillingRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invoice_number',
        'amount',
        'currency',
        'status',
        'line_items',
        'payment_method',
        'transaction_id',
        'due_date',
        'paid_at',
        'notes',
        'description',
        'description2',
    ];

    protected $casts = [
        'line_items' => 'array',
        'amount' => 'decimal:2',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: fn() => number_format($this->amount, 2) . ' ' . $this->currency
        );
    }

    public function markAsPaid(string $transactionId = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'transaction_id' => $transactionId,
        ]);
    }

    public function generateInvoiceNumber(): string
    {
        return 'INV-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
}
