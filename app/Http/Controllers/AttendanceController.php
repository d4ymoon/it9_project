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
        //
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);
    
        $employeeId = $validated['employee_id'];
        $today = Carbon::now()->toDateString();
        $now = Carbon::now('Asia/Singapore')->format('H:i:s');
    
        $attendance = Attendance::where('employee_id', $employeeId)
                                ->where('date', $today)
                                ->first();
    
        if ($attendance) {
            if ($attendance->time_out === null) {
                $attendance->time_out = $now;
                $attendance->save();
                return redirect()->back()->with('success', 'Time Out recorded successfully.');
            } else {
                return redirect()->back()->with('error', 'You have already logged out today.');
            }
        } else {
            $attendance = new Attendance();
            $attendance->employee_id = $employeeId;
            $attendance->date = $today;
            $attendance->time_in = $now;
            $attendance->status = 'Present';
            $attendance->save();
            return redirect()->back()->with('success', 'Time In recorded successfully.');
        }
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
