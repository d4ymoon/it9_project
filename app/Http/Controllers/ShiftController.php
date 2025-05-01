<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon; 

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $shifts = Shift::all();
        return view('shifts.index', compact('shifts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'start_time' => 'required|date_format:H:i',
        'end_time' => [
            'required',
            'date_format:H:i',
            function ($attribute, $value, $fail) use ($request) {
                $start = Carbon::createFromFormat('H:i', $request->start_time);
                $end = Carbon::createFromFormat('H:i', $value);

                if ($end <= $start) {
                    $end->addDay(); // only if end is before start (overnight)
                }

                if ($end <= $start) {
                    $fail('End time must be after start time.');
                }
            }
        ],
        'break_start_time' => [
            'nullable',
            'date_format:H:i',
            function ($attribute, $value, $fail) use ($request) {
                if (!$value || !$request->start_time || !$request->end_time) return;
            
                $start = Carbon::createFromFormat('H:i', $request->start_time);
                $end = Carbon::createFromFormat('H:i', $request->end_time);
                $breakStart = Carbon::createFromFormat('H:i', $value);
            
                // Handle overnight shift
                if ($end <= $start) $end->addDay();
                if ($breakStart < $start) $breakStart->addDay(); // Adjust break time into next day if needed
            
                if ($breakStart < $start || $breakStart > $end) {
                    $fail('Break start time must be within the shift time range.');
                }
            }
        ],
        'break_end_time' => [
            'nullable',
            'date_format:H:i',
            Rule::requiredIf(function () use ($request) {
                return $request->filled('break_start_time');
            }),
            function ($attribute, $value, $fail) use ($request) {
                if (!$value || !$request->start_time || !$request->end_time) return;

                $start = Carbon::createFromFormat('H:i', $request->start_time);
                $end = Carbon::createFromFormat('H:i', $request->end_time);
                $breakStart = $request->break_start_time ? Carbon::createFromFormat('H:i', $request->break_start_time) : null;
                $breakEnd = Carbon::createFromFormat('H:i', $value);

                if ($end <= $start) $end->addDay();
                if ($breakEnd <= $start) $breakEnd->addDay();
                if ($breakStart && $breakEnd <= $breakStart) {
                    $fail('Break end time must be after break start time.');
                }
                if ($breakEnd < $start || $breakEnd > $end) {
                    $fail('Break end time must be within the shift time range.');
                }
            }
        ],
    ]);

    Shift::create([
        'name' => $request->name,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'break_start_time' => $request->break_start_time,
        'break_end_time' => $request->break_end_time,
        'description' => $request->description,
    ]);

    return redirect()->route('shifts.index')->with('success', 'Shift added successfully!');
}

    /**
     * Display the specified resource.
     */
    public function show(Shift $shift)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shift $shift)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, $id)
{
    $request->merge([
        'start_time' => $request->start_time ? Carbon::parse($request->start_time)->format('H:i') : null,
        'end_time' => $request->end_time ? Carbon::parse($request->end_time)->format('H:i') : null,
        'break_start_time' => $request->break_start_time ? Carbon::parse($request->break_start_time)->format('H:i') : null,
        'break_end_time' => $request->break_end_time ? Carbon::parse($request->break_end_time)->format('H:i') : null,
    ]);
    $request->validate([
        'name' => 'required|string|max:255',
        'start_time' => 'required|date_format:H:i',
        'end_time' => [
            'required',
            'date_format:H:i',
            function ($attribute, $value, $fail) use ($request) {
                $start = Carbon::createFromFormat('H:i', $request->start_time);
                $end = Carbon::createFromFormat('H:i', $value);

                if ($end <= $start) $end->addDay();

                if ($end <= $start) {
                    $fail('End time must be after start time.');
                }
            }
        ],
        'break_start_time' => [
            'nullable',
            'date_format:H:i',
            function ($attribute, $value, $fail) use ($request) {
                if (!$value || !$request->start_time || !$request->end_time) return;
            
                $start = Carbon::createFromFormat('H:i', $request->start_time);
                $end = Carbon::createFromFormat('H:i', $request->end_time);
                $breakStart = Carbon::createFromFormat('H:i', $value);
            
                // Handle overnight shift
                if ($end <= $start) $end->addDay();
                if ($breakStart < $start) $breakStart->addDay(); // Adjust break time into next day if needed
            
                if ($breakStart < $start || $breakStart > $end) {
                    $fail('Break start time must be within the shift time range.');
                }
            }
        ],
        'break_end_time' => [
            'nullable',
            'date_format:H:i',
            Rule::requiredIf(function () use ($request) {
                return $request->filled('break_start_time');
            }),
            function ($attribute, $value, $fail) use ($request) {
                if (!$value || !$request->start_time || !$request->end_time) return;

                $start = Carbon::createFromFormat('H:i', $request->start_time);
                $end = Carbon::createFromFormat('H:i', $request->end_time);
                $breakStart = $request->break_start_time ? Carbon::createFromFormat('H:i', $request->break_start_time) : null;
                $breakEnd = Carbon::createFromFormat('H:i', $value);

                if ($end <= $start) $end->addDay();
                if ($breakEnd <= $start) $breakEnd->addDay();
                if ($breakStart && $breakEnd <= $breakStart) {
                    $fail('Break end time must be after break start time.');
                }
                if ($breakEnd < $start || $breakEnd > $end) {
                    $fail('Break end time must be within the shift time range.');
                }
            }
        ],
        'description' => 'nullable|string|max:500',
    ]);

    $shift = Shift::findOrFail($id);
    $shift->update([
        'name' => $request->name,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'break_start_time' => $request->break_start_time ?? null,
        'break_end_time' => $request->break_end_time ?? null,
        'description' => $request->description,
    ]);

    return redirect()->back()->with('success', 'Shift updated successfully.');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shift $shift)
    {
        //
    }
}
