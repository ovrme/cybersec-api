<?php

namespace App\Http\Controllers;

use App\Models\AttackSimulation;
use App\Models\SimulationInteraction;
use Illuminate\Http\Request;

class SimulationController extends Controller
{
    // Get all simulations
    public function index()
    {
        $simulations = AttackSimulation::where('is_active', true)->get();
        return response()->json($simulations);
    }

    // Get single simulation
    public function show($id)
    {
        $simulation = AttackSimulation::findOrFail($id);
        return response()->json($simulation);
    }

    // User interacts with simulation
    public function interact(Request $request, $id)
    {
        $request->validate([
            'fell_for_it' => 'required|boolean',
        ]);

        $simulation = AttackSimulation::findOrFail($id);

        $interaction = SimulationInteraction::create([
            'user_id'       => $request->user()->id,
            'simulation_id' => $id,
            'fell_for_it'   => $request->fell_for_it,
        ]);

        $message = $request->fell_for_it
            ? '⚠️ You fell for this attack! Learn from this experience.'
            : '✅ Great job! You correctly identified this as an attack.';

        return response()->json([
            'message'     => $message,
            'fell_for_it' => $request->fell_for_it,
            'simulation'  => $simulation->title,
            'tip'         => $this->getTip($simulation->type),
        ]);
    }

    // Get user simulation history
    public function history(Request $request)
    {
        $history = SimulationInteraction::where('user_id', $request->user()->id)
                                        ->with('simulation')
                                        ->latest()
                                        ->get();

        $stats = [
            'total'       => $history->count(),
            'fell_for_it' => $history->where('fell_for_it', true)->count(),
            'avoided'     => $history->where('fell_for_it', false)->count(),
        ];

        return response()->json([
            'stats'   => $stats,
            'history' => $history,
        ]);
    }

    // Admin: create simulation
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string',
            'type'    => 'required|in:phishing_email,fake_url,social_engineering',
            'content' => 'required|string',
        ]);

        $simulation = AttackSimulation::create($request->all());

        return response()->json([
            'message'    => 'Simulation created successfully',
            'simulation' => $simulation,
        ], 201);
    }

    private function getTip(string $type): string
    {
        return match($type) {
            'phishing_email'      => 'Always verify the sender email address and never click suspicious links.',
            'fake_url'            => 'Always check the URL carefully before entering any credentials.',
            'social_engineering'  => 'Never give out personal information to unverified sources.',
            default               => 'Stay alert and think before you click.',
        };
    }
}
