<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'description', 'color', 'is_active', 'allows_access'];

    protected $casts = [
        'is_active' => 'boolean',
        'allows_access' => 'boolean',
    ];

    public function staff()
    {
        return $this->hasMany(Staff::class, 'status_id');
    }
}
