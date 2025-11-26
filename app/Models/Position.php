<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'code', 'description', 'department_id', 'type', 'required_qualifications', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'required_qualifications' => 'array',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }
}
