<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        //
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request) {
                    // Validate that end_time is later than start_time
                    $start_time = \Carbon\Carbon::createFromFormat('H:i', $request->start_time);
                    $end_time = \Carbon\Carbon::createFromFormat('H:i', $value);
    
                    // If the end time is earlier than start time, assume it's the next day
                    if ($end_time <= $start_time) {
                        $end_time->addDay();  // Add one day to the end time
                    }
    
                    // Check if end time is still before start time (after adding a day)
                    if ($end_time->lte($start_time)) {
                        $fail('The end time field must be a time after start time.');
                    }
                }
            ],
            'break_start_time' => [
                'nullable',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && ($request->start_time && $request->end_time)) {
                        $start_time = \Carbon\Carbon::createFromFormat('H:i', $request->start_time);
                        $end_time = \Carbon\Carbon::createFromFormat('H:i', $request->end_time);
                        $break_start_time = \Carbon\Carbon::createFromFormat('H:i', $value);
    
                        // Ensure the break time is within the shift time range
                        if ($break_start_time < $start_time || $break_start_time > $end_time) {
                            $fail('Break start time must be within the shift time range.');
                        }
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
                    if ($value && $request->break_start_time) {
                        $break_start_time = \Carbon\Carbon::createFromFormat('H:i', $request->break_start_time);
                        $break_end_time = \Carbon\Carbon::createFromFormat('H:i', $value);
    
                        // Ensure break end time is after break start time
                        if ($break_end_time <= $break_start_time) {
                            $fail('Break end time must be after break start time.');
                        }
                    }
    
                    if ($value && ($request->start_time && $request->end_time)) {
                        $start_time = \Carbon\Carbon::createFromFormat('H:i', $request->start_time);
                        $end_time = \Carbon\Carbon::createFromFormat('H:i', $request->end_time);
                        $break_end_time = \Carbon\Carbon::createFromFormat('H:i', $value);
    
                        // Ensure break end time is within the shift time range
                        if ($break_end_time < $start_time || $break_end_time > $end_time) {
                            $fail('Break end time must be within the shift time range.');
                        }
                    }
                }
            ],
        ]);
    
        // Store the shift
        Shift::create([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'break_start_time' => $request->break_start_time ?? null,
            'break_end_time' => $request->break_end_time ?? null,
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
        //
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request) {
                    // Check if end time is earlier than start time (crossing midnight)
                    $start_time = \Carbon\Carbon::createFromFormat('H:i', $request->start_time);
                    $end_time = \Carbon\Carbon::createFromFormat('H:i', $value);
    
                    // If end time is earlier than start time, assume it's on the next day
                    if ($end_time <= $start_time) {
                        $end_time->addDay();  // Add one day to the end time
                    }
    
                    // Check if end time is still before start time (after adding the day)
                    if ($end_time->lte($start_time)) {
                        $fail('The end time field must be a time after start time.');
                    }
                }
            ],
            'break_start_time' => [
                'nullable',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && ($request->start_time && $request->end_time)) {
                        $start_time = \Carbon\Carbon::createFromFormat('H:i', $request->start_time);
                        $end_time = \Carbon\Carbon::createFromFormat('H:i', $request->end_time);
                        $break_start_time = \Carbon\Carbon::createFromFormat('H:i', $value);
    
                        if ($break_start_time < $start_time || $break_start_time > $end_time) {
                            $fail('Break start time must be within the shift time range.');
                        }
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
                    if ($value && $request->break_start_time) {
                        $break_start_time = \Carbon\Carbon::createFromFormat('H:i', $request->break_start_time);
                        $break_end_time = \Carbon\Carbon::createFromFormat('H:i', $value);
    
                        if ($break_end_time <= $break_start_time) {
                            $fail('Break end time must be after break start time.');
                        }
                    }
    
                    if ($value && ($request->start_time && $request->end_time)) {
                        $start_time = \Carbon\Carbon::createFromFormat('H:i', $request->start_time);
                        $end_time = \Carbon\Carbon::createFromFormat('H:i', $request->end_time);
                        $break_end_time = \Carbon\Carbon::createFromFormat('H:i', $value);
    
                        if ($break_end_time < $start_time || $break_end_time > $end_time) {
                            $fail('Break end time must be within the shift time range.');
                        }
                    }
                }
            ],
            'description' => 'nullable|string|max:500',
        ]);
    
        // Proceed with the shift update logic
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
