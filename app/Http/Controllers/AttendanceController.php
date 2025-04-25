<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
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
    $validated = $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'shift' => 'required|in:morning,afternoon',
    ]);
    
    $employeeId = $validated['employee_id'];
    $shift = $validated['shift'];
    $today = Carbon::today('Asia/Singapore');
    $now = Carbon::now('Asia/Singapore');
    $currentTime = Carbon::now('Asia/Singapore');

    // Fetch or create today's attendance record
    $attendance = Attendance::firstOrNew([
        'employee_id' => $employeeId,
        'date' => $today
    ]);

    if ($shift == 'morning') {
        if (!$attendance->morning_time_in) {
            // Morning login
            $attendance->morning_time_in = $currentTime;
            $attendance->status = null; // Status remains null until logout
            $attendance->save();
            return redirect()->back()->with('success', 'Morning Time In recorded.');
        }

        if ($attendance->morning_time_in && !$attendance->morning_time_out) {
            // Check if logout is still allowed (before midnight)
            if ($currentTime->isSameDay($attendance->morning_time_in)) {
                $attendance->morning_time_out = $currentTime;
                $attendance->status = 'Present'; // Only mark present if logged out within same day
                $attendance->save();
                return redirect()->back()->with('success', 'Morning Time Out recorded.');
            } else {
                return redirect()->back()->with('error', 'Cannot logout for morning shift. Deadline passed.');
            }
        }

        return redirect()->back()->with('error', 'Morning attendance already completed.');
    }

    if ($shift == 'afternoon') {
        if (!$attendance->afternoon_time_in) {
            // Afternoon login
            $attendance->afternoon_time_in = $currentTime;
            $attendance->status = null; // Status remains null until logout
            $attendance->save();
            return redirect()->back()->with('success', 'Afternoon Time In recorded.');
        }

        if ($attendance->afternoon_time_in && !$attendance->afternoon_time_out) {
            // Check if logout is still allowed (before 8:00 AM next day)
            $deadline = Carbon::parse($attendance->afternoon_time_in)->addDay()->setTime(8, 0, 0); // 8:00 AM next day

            if ($currentTime->lessThanOrEqualTo($deadline)) {
                $attendance->afternoon_time_out = $currentTime;
                $attendance->status = 'Present'; // Only mark present if logout within allowed time
                $attendance->save();
                return redirect()->back()->with('success', 'Afternoon Time Out recorded.');
            } else {
                return redirect()->back()->with('error', 'Cannot logout for afternoon shift. Deadline passed.');
            }
        }

        return redirect()->back()->with('error', 'Afternoon attendance already completed.');
    }

    return redirect()->back()->with('error', 'Invalid operation.');
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
