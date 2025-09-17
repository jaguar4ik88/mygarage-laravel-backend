<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpensesHistory extends Model
{
    use HasFactory;

    protected $table = 'expenses_history';

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'type',
        'expense_type_id',
        'title',
        'description',
        'cost',
        'mileage',
        'service_date',
        'station_name',
    ];

    protected $casts = [
        'service_date' => 'datetime',
        'cost' => 'decimal:2',
        'mileage' => 'integer',
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
}