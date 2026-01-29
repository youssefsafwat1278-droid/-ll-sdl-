<?php

namespace App\Http\Controllers;

use App\Models\Patrol;
use App\Models\Scout;

class PatrolController extends Controller
{
    public function index()
    {
        $patrols = Patrol::with('scouts')
            ->orderBy('rank')
            ->get();

        return view('patrols.index', compact('patrols'));
    }

    public function show($patrolId)
    {
        $patrol = Patrol::with(['scouts' => function($query) {
            $query->orderBy('total_points', 'desc');
        }])->findOrFail($patrolId);

        return view('patrols.show', compact('patrol'));
    }
}