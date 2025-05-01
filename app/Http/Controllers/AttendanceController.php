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
    $employeeId = $request->employee_id;
    $now = Carbon::now();
    $date = $now->toDateString();

    $employee = Employee::with('shift')->findOrFail($employeeId);
    $shift = $employee->shift;

    if (!$shift) {
        return response()->json(['message' => 'No shift assigned to this employee'], 422);
    }


    // Shift times
    $shiftStart = Carbon::parse($date . ' ' . $shift->start_time);
    $breakStart = $shift->break_start_time ? Carbon::parse($date . ' ' . $shift->break_start_time) : null;
    $breakEnd = $shift->break_end_time ? Carbon::parse($date . ' ' . $shift->break_end_time) : null;
    $shiftEnd = Carbon::parse($date . ' ' . $shift->end_time);
    
    // In case end_time is after midnight (e.g. 06:00), push to next day
    if ($shiftEnd->lt($shiftStart)) {
        $shiftEnd->addDay();
    }

    $nextShiftStart = $shiftStart->copy()->addDay();

    // Get today's attendance
    $attendance = Attendance::where('employee_id', $employeeId)
        ->whereDate('date', $date)
        ->first();

    if (!$attendance) {
        // First login
        if ($now->between($breakStart, $shiftEnd)) {
            // Logged in during second half
            $attendance = Attendance::create([
                'employee_id' => $employeeId,
                'date' => $date,
                'break_in' => $now,
            ]);
            return response()->json(['message' => 'Logged in (second half)', 'attendance' => $attendance]);
        } elseif ($now->lt($breakStart)) {
            // Logged in during first half
            $attendance = Attendance::create([
                'employee_id' => $employeeId,
                'date' => $date,
                'time_in' => $now,
            ]);
            return response()->json(['message' => 'Logged in (first half)', 'attendance' => $attendance]);
        } else {
            // Invalid login attempt after shift ends
            return response()->json(['message' => 'Cannot login after shift without prior login'], 403);
        }
    }

    // Log break out
    if ($attendance->time_in && !$attendance->break_out && $now->between($breakStart, $breakEnd)) {
        $attendance->break_out = $now;
        $attendance->save();
        return response()->json(['message' => 'Break out recorded', 'attendance' => $attendance]);
    }

    // Log break in (2nd half login)
    if ($attendance->break_out && !$attendance->break_in && $now->between($breakEnd, $shiftEnd->copy()->addHours(6))) {
        $attendance->break_in = $now;
        $attendance->save();
        return response()->json(['message' => 'Break in recorded', 'attendance' => $attendance]);
    }

    // Log time out
    if ($attendance->break_in && !$attendance->time_out && $now->gte($breakEnd)) {
        $attendance->time_out = $now;

        // Determine status
        if ($attendance->time_in && $attendance->break_out && $attendance->break_in && $attendance->time_out) {
            $attendance->status = 'Present';
        } elseif ($attendance->time_in && $attendance->break_out && !$attendance->break_in && $now->gt($nextShiftStart)) {
            $attendance->status = 'Absent';
        } elseif ($attendance->break_in && !$attendance->time_out && $now->gt($nextShiftStart)) {
            $attendance->status = 'Absent';
        } elseif ($attendance->time_in && $attendance->break_out && !$attendance->break_in && $attendance->time_out) {
            $attendance->status = 'Half Day';
        }

        $attendance->save();
        return response()->json(['message' => 'Time out recorded', 'attendance' => $attendance]);
    }

    // Invalid or redundant action
    return response()->json(['message' => 'No applicable attendance action for this time', 'attendance' => $attendance]);

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
