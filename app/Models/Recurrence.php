<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recurrence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'value',
        'type',
        'frequency',
        'interval',
        'anchor_day',
        'start_date',
        'end_date',
        'max_occurrences',
        'occurrences_count',
        'next_run_date',
        'last_generated_at',
        'status',
        'note',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_run_date' => 'date',
        'last_generated_at' => 'datetime',
        'max_occurrences' => 'integer',
        'occurrences_count' => 'integer',
        'interval' => 'integer',
        'anchor_day' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function contas()
    {
        return $this->hasMany(Conta::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDue($query, $date = null)
    {
        $targetDate = $date ?? now()->toDateString();
        return $query->active()->where('next_run_date', '<=', $targetDate);
    }

    public function getFrequencyLabelAttribute(): string
    {
        return match ($this->frequency) {
            'weekly' => 'Semanal',
            'biweekly' => 'Quinzenal (a cada 15 dias)',
            'monthly' => 'Mensal',
            'yearly' => 'Anual',
            'custom' => "A cada {$this->interval} dias",
            default => ucfirst($this->frequency),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Ativa',
            'paused' => 'Pausada',
            'completed' => 'Concluída',
            'canceled' => 'Cancelada',
            default => ucfirst($this->status),
        };
    }
}
