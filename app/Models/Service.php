<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'prefix',
        'description',
        'estimated_time_minutes',
        'color',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'estimated_time_minutes' => 'integer',
    ];

    public function counters(): HasMany
    {
        return $this->hasMany(Counter::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(QueueTicket::class);
    }

    public function todayTickets(): HasMany
    {
        return $this->hasMany(QueueTicket::class)->whereDate('queue_date', today());
    }

    public function waitingTickets(): HasMany
    {
        return $this->todayTickets()->where('status', 'waiting')->orderBy('sequence_number', 'asc');
    }

    public function hasActiveCounter(): bool
    {
        return $this->counters()->where('status', 'active')->exists();
    }

    public function getNextSequenceNumber(): int
    {
        $lastSequence = $this->todayTickets()->max('sequence_number') ?? 0;
        return $lastSequence + 1;
    }

    public function formatTicketNumber(int $sequenceNumber): string
    {
        $prefix = $this->prefix ?: $this->code;
        return sprintf('%s-%03d', strtoupper($prefix), $sequenceNumber);
    }
}
