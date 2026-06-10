<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttackSimulation extends Model
{
    protected $fillable = [
        'title',
        'type',
        'description',
        'content',
        'is_active',
    ];

    public function interactions()
    {
        return $this->hasMany(SimulationInteraction::class, 'simulation_id');
    }
}
