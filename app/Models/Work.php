<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    use HasFactory;

    protected $fillable = [
        'thumb',
        'title',
        'publication_date',
        'institution_id',
        'file',
        'grade',
        'subject',
        'type',
        'language',
        'license',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function researchers()
    {
        return $this->belongsToMany(Researcher::class, 'work_researchers', 'work_id', 'researcher_id');
    }
}
