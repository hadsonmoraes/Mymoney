<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = 'category';

    protected $fillable = [
        'name', 'user_id'
    ];

    public function conta()
    {
        return $this->hasMany(Conta::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
