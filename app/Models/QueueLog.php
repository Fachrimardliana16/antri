<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QueueLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'service_id',
        'counter_id',
        'operator_id',
        'action', // created, called, recalled, serving, completed, skipped, transferred
        'wait_duration_seconds',
        'service_duration_seconds',
        'notes',
        'logged_at',
    ];

    protected $casts = [
        'wait_duration_seconds' => 'integer',
        'service_duration_seconds' => 'integer',
        'logged_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(QueueTicket::class, 'ticket_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function counter(): BelongsTo
    {
        return $this->belongsTo(Counter::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}
