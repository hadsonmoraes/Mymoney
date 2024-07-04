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
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
