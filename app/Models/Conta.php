<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;


class Conta extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'value',
        'maturity',
        'situation',
        'category_id',
        'type',
        'note',
        'image',
        'user_id',
        'fixed',
        'repeat',
        'recurrence_id',
        'parent_id',
        'repeat_group_id',
        'repeat_index',
        'repeat_total',
    ];

    protected $casts = [
        'fixed' => 'boolean',
        'maturity' => 'date',
        'repeat_index' => 'integer',
        'repeat_total' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function recurrence()
    {
        return $this->belongsTo(Recurrence::class);
    }

    public function parent()
    {
        return $this->belongsTo(Conta::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Conta::class, 'parent_id');
    }

    public function internalNotifications()
    {
        return $this->hasMany(InternalNotification::class);
    }

    public function getIsRepeatedAttribute(): bool
    {
        return !empty($this->repeat_group_id);
    }

    public function getIsRecurringAttribute(): bool
    {
        return !is_null($this->recurrence_id) || (bool) $this->fixed;
    }

    public function getRepeatLabelAttribute(): ?string
    {
        if ($this->repeat_index && $this->repeat_total) {
            return "Repetição {$this->repeat_index}/{$this->repeat_total}";
        }

        return null;
    }

    public function getStatusAttribute()
    {
        return match ($this->situation) {
            'paid' => 'success',
            'pending' => 'warning',
            default => 'danger',
        };
    }

    public function getSituationNameAttribute()
    {
        return match ($this->situation) {
            'paid' => 'Pago',
            'pending' => 'Pendente',
            default => 'Cancelado',
        };
    }
}
