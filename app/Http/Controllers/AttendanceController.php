<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Attendance::with('employee')->latest();

        // Filter by month if selected
        if ($request->filled('month')) {
            $date = Carbon::createFromFormat('Y-m', $request->month);
            $query->whereYear('date', $date->year)
                  ->whereMonth('date', $date->month);
        }

        // Filter by specific day if selected
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        // Filter by employee name if search term provided
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $attendances = $query->paginate(10);

        // Get min and max dates for the filters
        $dateRange = Attendance::selectRaw('MIN(date) as min_date, MAX(date) as max_date')->first();
        $minDate = $dateRange->min_date ? Carbon::parse($dateRange->min_date)->format('Y-m') : null;
        $maxDate = $dateRange->max_date ? Carbon::parse($dateRange->max_date)->format('Y-m') : null;

        return view('attendance.index', compact('attendances', 'minDate', 'maxDate'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('attendance.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'current_time' => 'required',
        ]);

        $employee = Employee::with('shift')->findOrFail($validated['employee_id']);
        
        if (!$employee->shift) {
            return redirect()->back()->with('error', 'No shift assigned to this employee.');
        }

        $now = Carbon::createFromFormat('H:i:s', $validated['current_time']);
        $today = Carbon::today();
        $date = $today->format('Y-m-d');

        // Get shift times for today
        $shiftStart = Carbon::parse($date . ' ' . $employee->shift->start_time);
        $breakStart = Carbon::parse($date . ' ' . $employee->shift->break_start_time);
        $breakEnd = Carbon::parse($date . ' ' . $employee->shift->break_end_time);
        $shiftEnd = Carbon::parse($date . ' ' . $employee->shift->end_time);

        // Handle shifts that might end after midnight
        if ($shiftEnd->lt($shiftStart)) {
            $shiftEnd->addDay();
        }

        $nextShiftStart = $shiftStart->copy()->addDay();

        // Get today's attendance record
        $attendance = Attendance::where('employee_id', $validated['employee_id'])
            ->whereDate('date', $date)
            ->first();

        if (!$attendance) {
            // First login attempt of the day
            if ($now->between($breakStart, $shiftEnd)) {
                // Logged in during second half
                $attendance = new Attendance([
                    'employee_id' => $validated['employee_id'],
                    'date' => $date,
                    'break_in' => $now,
                    'status' => 'Half Day'
                ]);
                $attendance->save();
                return redirect()->back()->with('success', 'Logged in for second half of shift.');
            } elseif ($now->lt($breakStart)) {
                // Logged in during first half
                $attendance = new Attendance([
                    'employee_id' => $validated['employee_id'],
                    'date' => $date,
                    'time_in' => $now,
                    'status' => 'Present'
                ]);
                $attendance->save();
                return redirect()->back()->with('success', 'Logged in for first half of shift.');
            } else {
                // Trying to login after shift ends without prior login
                return redirect()->back()->with('error', 'Cannot start attendance after shift hours.');
            }
        }

        // Handle existing attendance record
        if ($attendance->time_in && !$attendance->break_out && $now->between($breakStart, $breakEnd)) {
            // First half logout (break start)
            $attendance->break_out = $now;
            $attendance->save();
            return redirect()->back()->with('success', 'Break time started.');
        }

        if ($attendance->break_out && !$attendance->break_in && $now->between($breakEnd, $shiftEnd->copy()->addHours(6))) {
            // Second half login (break end)
            $attendance->break_in = $now;
            $attendance->save();
            return redirect()->back()->with('success', 'Break ended, second half started.');
        }

        if ($attendance->break_in && !$attendance->time_out && $now->gte($breakEnd)) {
            // Final logout
            $attendance->time_out = $now;

            // Determine final status
            if ($attendance->time_in && $attendance->break_out && $attendance->break_in && $attendance->time_out) {
                $attendance->status = 'Present';
            } elseif ($attendance->time_in && $attendance->break_out && !$attendance->break_in && $now->gt($nextShiftStart)) {
                $attendance->status = 'Absent';
            } elseif ($attendance->break_in && !$attendance->time_out && $now->gt($nextShiftStart)) {
                $attendance->status = 'Absent';
            } elseif ($attendance->time_in && $attendance->break_out && !$attendance->break_in) {
                $attendance->status = 'Half Day';
            }

            $attendance->save();
            return redirect()->back()->with('success', 'Shift completed. Time out recorded.');
        }

        return redirect()->back()->with('error', 'Invalid attendance action for current time.');
    }

    /**
     * Get appropriate success message based on attendance state
     */
    private function getSuccessMessage($attendance)
    {
        if ($attendance->time_in && !$attendance->break_out) {
            return 'First half attendance started.';
        } elseif ($attendance->time_in && $attendance->break_out && !$attendance->break_in) {
            return 'First half completed. Break started.';
        } elseif ($attendance->break_in && !$attendance->time_out) {
            return 'Second half attendance started.';
        } elseif ($attendance->time_out) {
            return 'Attendance completed for the day.';
        }
        return 'Attendance logged successfully.';
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
        $attendance = Attendance::findOrFail($id);
        
        $validated = $request->validate([
            'date' => 'required|date',
            'time_in' => 'nullable',
            'break_out' => 'nullable',
            'break_in' => 'nullable',
            'time_out' => 'nullable',
            'status' => 'required|in:Present,Absent,Half Day,Leave',
        ]);

        if ($validated['status'] === 'Leave') {
            $validated['time_in'] = null;
            $validated['break_out'] = null;
            $validated['break_in'] = null;
            $validated['time_out'] = null;
        } else {
            $fields = ['time_in', 'break_out', 'break_in', 'time_out'];
            foreach ($fields as $field) {
                if (!empty($validated[$field])) {
                    $validated[$field] = $validated['date'] . ' ' . $validated[$field];
                } else {
                    $validated[$field] = null;
                }
            }
        }

        $attendance->update($validated);

        return redirect()->route('attendances.index')
            ->with('success', 'Attendance record updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return redirect()->route('attendances.index')
            ->with('success', 'Attendance record deleted successfully');
    }

    public function employeeAttendance()
    {
        $employee = Auth::user()->employee;
        $attendances = Attendance::where('employee_id', $employee->id)
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('employee.attendance.index', compact('attendances'));
    }
}
