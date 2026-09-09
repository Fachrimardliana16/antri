<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Counter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'number',
        'service_id',
        'current_operator_id',
        'status', // active, break, closed
        'current_ticket_id',
    ];

    protected $casts = [
        'number' => 'integer',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function currentOperator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_operator_id');
    }

    public function currentTicket(): BelongsTo
    {
        return $this->belongsTo(QueueTicket::class, 'current_ticket_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(QueueTicket::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(QueueLog::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isBreak(): bool
    {
        return $this->status === 'break';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }
}
