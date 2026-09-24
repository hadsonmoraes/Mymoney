<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'conta_id',
        'type',
        'title',
        'message',
        'reference_date',
        'read_at',
    ];

    protected $casts = [
        'reference_date' => 'date',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function conta()
    {
        return $this->belongsTo(Conta::class);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function markAsRead(): bool
    {
        if (is_null($this->read_at)) {
            return $this->update(['read_at' => now()]);
        }
        return true;
    }

    public function getTypeBadgeAttribute(): string
    {
        return match ($this->type) {
            'overdue' => 'danger',
            'due_today' => 'danger',
            'upcoming' => 'warning',
            default => 'info',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'overdue' => 'Vencido',
            'due_today' => 'Vence hoje',
            'upcoming' => 'Próximo do vencimento',
            default => 'Aviso',
        };
    }
}
