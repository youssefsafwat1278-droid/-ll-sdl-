<?php

namespace App\Http\Controllers;

use App\Models\Gameweek;

class GameweekController extends Controller
{
    public function index()
    {
        $gameweeks = Gameweek::orderBy('gameweek_number', 'desc')->get();
        return view('gameweeks.index', compact('gameweeks'));
    }

    public function current()
    {
        $gameweek = Gameweek::where('is_current', true)->first();

        if (!$gameweek) {
            return view('gameweeks.current', ['message' => 'لا يوجد أسبوع نشط']);
        }

        return view('gameweeks.current', compact('gameweek'));
    }

    public function show($gameweekId)
    {
        $gameweek = Gameweek::with(['performances.scout'])->findOrFail($gameweekId);
        return view('gameweeks.show', compact('gameweek'));
    }
}