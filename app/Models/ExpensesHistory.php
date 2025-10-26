<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ExpensesHistory extends Model
{
    use HasFactory;

    protected $table = 'expenses_history';

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'expense_type_id',
        'description',
        'cost',
        'service_date',
        'station_name',
        'receipt_photo',
    ];

    protected $casts = [
        'service_date' => 'datetime',
        'cost' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function expenseType(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id');
    }

    /**
     * Check if expense has receipt
     */
    public function hasReceipt(): bool
    {
        return !empty($this->receipt_photo);
    }

    /**
     * Get receipt URL
     */
    public function getReceiptUrlAttribute(): ?string
    {
        if (!$this->receipt_photo) {
            return null;
        }

        // Если файл в публичной папке, возвращаем прямой URL
        if (str_starts_with($this->receipt_photo, 'receipts/')) {
            return Storage::disk('public')->url($this->receipt_photo);
        }

        // Для старых файлов в приватной папке используем API endpoint
        return url('api/expenses/' . $this->id . '/receipt');
    }

    /**
     * Get full receipt path for storage
     */
    public function getFullReceiptPathAttribute(): ?string
    {
        if (!$this->receipt_photo) {
            return null;
        }

        return storage_path('app/' . $this->receipt_photo);
    }

    /**
     * Delete receipt file when model is deleted
     */
    protected static function booted()
    {
        static::deleting(function ($expense) {
            if ($expense->receipt_photo && \Storage::exists($expense->receipt_photo)) {
                \Storage::delete($expense->receipt_photo);
            }
        });
    }
}