<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimulationInteraction extends Model
{
    protected $fillable = [
        'user_id',
        'simulation_id',
        'fell_for_it',
    ];

    public function simulation()
    {
        return $this->belongsTo(AttackSimulation::class, 'simulation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
