<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class QueueTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'sequence_number',
        'service_id',
        'counter_id',
        'operator_id',
        'status', // waiting, calling, serving, completed, skipped, transferred
        'tracking_token',
        'queue_date',
        'called_at',
        'served_at',
        'completed_at',
        'transferred_to_service_id',
    ];

    protected $casts = [
        'sequence_number' => 'integer',
        'queue_date' => 'date',
        'called_at' => 'datetime',
        'served_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($ticket) {
            if (empty($ticket->tracking_token)) {
                $ticket->tracking_token = Str::random(32);
            }
            if (empty($ticket->queue_date)) {
                $ticket->queue_date = today();
            }
        });
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

    public function transferredToService(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'transferred_to_service_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(QueueLog::class, 'ticket_id');
    }

    public function getTicketsAheadCount(): int
    {
        if ($this->status !== 'waiting') {
            return 0;
        }

        return static::where('service_id', $this->service_id)
            ->whereDate('queue_date', $this->queue_date)
            ->where('status', 'waiting')
            ->where('sequence_number', '<', $this->sequence_number)
            ->count();
    }

    public function getEstimatedWaitTimeMinutes(): int
    {
        $ahead = $this->getTicketsAheadCount();
        $rate = $this->service ? $this->service->estimated_time_minutes : 5;
        return ($ahead + 1) * $rate;
    }

    public function getVoiceSpokenText(): string
    {
        $counterNumber = $this->counter ? $this->counter->number : 1;
        $cleanNumber = $this->ticket_number;
        // e.g. "Nomor antrean A satu menuju loket dua"
        return "Nomor antrean {$cleanNumber}, menuju loket {$counterNumber}";
    }
}
