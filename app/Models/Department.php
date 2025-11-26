<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'description', 'manager_id', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }
}
