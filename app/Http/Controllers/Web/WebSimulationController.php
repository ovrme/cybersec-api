<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AttackSimulation;
use App\Models\SimulationInteraction;
use Illuminate\Http\Request;

class WebSimulationController extends Controller
{
    public function index(Request $request)
    {
        $simulations = AttackSimulation::where('is_active', true)->get();

        $history = SimulationInteraction::where('user_id', $request->user()->id)
                        ->with('simulation')
                        ->latest()
                        ->get();

        $stats = [
            'total'       => $history->count(),
            'fell_for_it' => $history->where('fell_for_it', true)->count(),
            'avoided'     => $history->where('fell_for_it', false)->count(),
        ];

        return view('simulations.index', compact('simulations', 'history', 'stats'));
    }

    public function show($id)
    {
        $simulation = AttackSimulation::where('is_active', true)->findOrFail($id);

        return view('simulations.show', compact('simulation'));
    }

    public function interact(Request $request, $id)
    {
        $request->validate(['fell_for_it' => 'required|boolean']);

        $simulation = AttackSimulation::findOrFail($id);

        SimulationInteraction::create([
            'user_id'       => $request->user()->id,
            'simulation_id' => $id,
            'fell_for_it'   => $request->boolean('fell_for_it'),
        ]);

        $tip = match ($simulation->type) {
            'phishing_email'     => 'Always verify the sender address and never click suspicious links.',
            'fake_url'           => 'Always check the URL carefully before entering credentials.',
            'social_engineering' => 'Never give out personal information to unverified sources.',
            default              => 'Stay alert and think before you click.',
        };

        $message = $request->boolean('fell_for_it')
            ? 'You fell for this one — but now you know the signs.'
            : 'Nicely done — you correctly identified the attack.';

        return view('simulations.result', [
            'simulation' => $simulation,
            'fellForIt'  => $request->boolean('fell_for_it'),
            'message'    => $message,
            'tip'        => $tip,
        ]);
    }
}
