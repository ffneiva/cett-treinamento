<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'fantasy_name',
        'corporate_name',
        'cnpj',
        'type',
    ];

    public function works()
    {
        return $this->hasMany(Work::class);
    }

    public function researchers()
    {
        return $this->hasMany(Researcher::class);
    }
}
