<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timetable;
use Carbon\Carbon;


class TimetableController extends Controller
{
    public function index()
    {
        $timetables = Timetable::all();
        $startOfWeek = Carbon::now()->startOfWeek();
        $dates = [];

        for ($i = 0; $i < 5; $i++) {
            $dates[] = $startOfWeek->copy()->addDays($i)->format('n/j');
        }
        return view('timetables.TimetableIndex', compact('timetables', 'dates'));
    }

    public function create()
    {
        return view('timetables.TimetableCreate');
    }

    public function store(Request $request)
    {

    }

    public function edit(Timetable $timetable)
    {

    }

    public function update(Request $request, Timetable $timetable)
    {

    }

}
