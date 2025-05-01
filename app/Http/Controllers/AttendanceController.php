<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon; 

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $attendances = Attendance::with('employee')->latest()->get();
        return view('attendance.index', compact('attendances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('attendance.create');
    }

    /**
     * Store a newly created resource in storage.
     */

public function store(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
    ]);

    $employee = Employee::with('shift')->findOrFail($request->employee_id);
    $shift = $employee->shift;

    if (!$shift) {
        return back()->with('error', 'Employee has no shift assigned.');
    }

    $now = Carbon::now('Asia/Singapore');
    $today = $now->toDateString();

    // Construct shift time slots for today in Singapore timezone
    $start = Carbon::parse("$today {$shift->start_time}", 'Asia/Singapore');
    $breakStart = Carbon::parse("$today {$shift->break_start_time}", 'Asia/Singapore');
    $breakEnd = Carbon::parse("$today {$shift->break_end_time}", 'Asia/Singapore');
    $end = Carbon::parse("$today {$shift->end_time}", 'Asia/Singapore');

    // Retrieve or create today's attendance
    $attendance = Attendance::firstOrCreate([
        'employee_id' => $employee->id,
        'date' => $today,
    ]);

    // Determine what to log
    if (
        !$attendance->time_in &&
        (
            $now->between($start->subHour(), $breakStart) ||         // First half
            $now->between($breakEnd, $end)                           // Second half (partial/afternoon)
        )
    ) {
        $attendance->time_in = $now;
        $msg = 'Time-in recorded.';
    } elseif (
        !$attendance->break_out &&
        $now->between($breakStart->subHour(), $breakStart->addHour())
    ) {
        $attendance->break_out = $now;
        $msg = 'Break-out recorded.';
    } elseif (
        !$attendance->break_in &&
        $now->between($breakEnd->subHour(), $breakEnd->addHour())
    ) {
        $attendance->break_in = $now;
        $msg = 'Break-in recorded.';
    } elseif (
        !$attendance->time_out &&
        $now->between($end->subHour(), $end->addHour())
    ) {
        $attendance->time_out = $now;
        $msg = 'Time-out recorded.';
    } else {
        $msg = 'No applicable attendance action for this time.';
    }

    $attendance->save();

    return back()->with('success', $msg);
}

    
    
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    
}
