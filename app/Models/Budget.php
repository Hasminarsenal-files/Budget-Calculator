<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'user_id',
        'name',
        'type',
        'total_amount',
        'start_date',
        'end_date',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function budgetCategories(): HasMany
    {
        return $this->hasMany(BudgetCategory::class, 'budget_id', 'uuid');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'budget_id', 'uuid');
    }

    public function getSpentAmountAttribute(): float
    {
        return (float) $this->transactions()->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return (float) ($this->total_amount - $this->spent_amount);
    }

    public function getRemainingPercentageAttribute(): float
    {
        if ($this->total_amount <= 0) return 0;
        return max(0, round(($this->remaining_amount / $this->total_amount) * 100, 1));
    }
}
