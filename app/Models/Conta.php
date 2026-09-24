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
        'installment_group_id',
        'installment_number',
        'installments_total',
    ];

    protected $casts = [
        'fixed' => 'boolean',
        'maturity' => 'date',
        'repeat_index' => 'integer',
        'repeat_total' => 'integer',
        'installment_number' => 'integer',
        'installments_total' => 'integer',
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

    public function getIsInstallmentAttribute(): bool
    {
        return !empty($this->installment_group_id) || (!is_null($this->installment_number) && !is_null($this->installments_total));
    }

    public function getInstallmentLabelAttribute(): ?string
    {
        if ($this->installment_number && $this->installments_total) {
            return "{$this->installment_number}/{$this->installments_total}";
        }

        return null;
    }

    /**
     * Detecta se o nome/descrição possui um padrão textual de parcelamento (ex: "Cartão - 3/12").
     * Apenas para sugerir configuração ao usuário sem alterar o banco de dados automaticamente.
     */
    public function detectInstallmentPattern(): ?array
    {
        if ($this->is_installment) {
            return null; // Já está estruturado
        }

        if (preg_match('/(?:parcela\s*|\-\s*|\#\s*)?(\d{1,3})\s*(?:\/|\s*de\s*)\s*(\d{1,3})/i', $this->name, $matches)) {
            $current = (int) $matches[1];
            $total = (int) $matches[2];
            if ($current > 0 && $total >= $current && $total <= 360) {
                $cleanName = trim(preg_replace('/(?:\s*\-\s*)?(?:parcela\s*|\#\s*)?\d{1,3}\s*(?:\/|\s*de\s*)\s*\d{1,3}/i', '', $this->name));
                return [
                    'current' => $current,
                    'total' => $total,
                    'clean_name' => $cleanName ?: $this->name,
                ];
            }
        }

        return null;
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
