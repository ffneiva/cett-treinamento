<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Researcher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'birth_date',
        'sex',
        'cpf',
        'university',
        'institution_id',
    ];

    public function works()
    {
        return $this->belongsToMany(Work::class, 'work_researchers', 'researcher_id', 'work_id');
    }

    public function institution()
    {
        return $this->belongsTo(institution::class);
    }
}
