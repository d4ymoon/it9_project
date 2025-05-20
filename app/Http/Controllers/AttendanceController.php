<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


// Set Manila timezone constant for reuse
define('MANILA_TZ', 'Asia/Manila');

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
        try {
            // Set current date if not provided
            if (!$request->filled('date')) {
                $request->merge(['date' => Carbon::now()->format('Y-m-d')]);
            }

            // Validate request
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'date' => 'required|date',
                'type' => 'required|in:time_in,time_out,break_out,break_in',
            ]);

            // Get employee with shift
            $employee = Employee::with('shift')->findOrFail($validated['employee_id']);
            $shift = $employee->shift;

            if (!$shift) {
                return redirect()->back()
                    ->with('error', 'Employee has no assigned shift.')
                    ->withInput();
            }

            // Find or create attendance record
            $attendance = Attendance::firstOrCreate([
                'employee_id' => $validated['employee_id'],
                'date' => $validated['date'],
            ], [
                'status' => 'Incomplete'
            ]);

            // Check if the time entry already exists
            $timeField = $validated['type'];
            if ($attendance->$timeField) {
                return redirect()->back()
                    ->with('error', ucfirst(str_replace('_', ' ', $timeField)) . ' already recorded for today.')
                    ->withInput();
            }

            // Check for proper sequence
            switch ($timeField) {
                case 'time_in':
                    // No prerequisites for time in
                    break;
                case 'break_out':
                    if (!$attendance->time_in) {
                        return redirect()->back()
                            ->with('error', 'You must time in before taking a break.')
                            ->withInput();
                    }
                    break;
                case 'break_in':
                    if (!$attendance->break_out) {
                        return redirect()->back()
                            ->with('error', 'You must break out before breaking in.')
                            ->withInput();
                    }
                    break;
                case 'time_out':
                    if (!$attendance->time_in) {
                        return redirect()->back()
                            ->with('error', 'You must time in before timing out.')
                            ->withInput();
                    }
                    if ($attendance->break_out && !$attendance->break_in) {
                        return redirect()->back()
                            ->with('error', 'You must break in before timing out.')
                            ->withInput();
                    }
                    break;
            }

            // Set the appropriate time field based on type
            $now = Carbon::now();
            $attendance->$timeField = $now;

            // Update status based on attendance state
            if ($attendance->time_out) {
                $attendance->status = 'Present';
            } else {
                $attendance->status = 'Incomplete';
            }

            // Calculate hours
            $this->calculateHours($attendance);

            return redirect()->back()->with('success', $this->getSuccessMessage($attendance));
        } catch (\Exception $e) {
            Log::error('Error saving attendance: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'An error occurred while saving the attendance record: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function calculateHours(Attendance $attendance)
    {
        if (!$attendance->time_in || !$attendance->time_out) {
            $attendance->total_hours = 0;
            $attendance->regular_hours = 0;
            $attendance->overtime_hours = 0;
            $attendance->save();
            return;
        }

        $timeIn = Carbon::parse($attendance->time_in);
        $timeOut = Carbon::parse($attendance->time_out);
        
        // Get employee's shift
        $shift = $attendance->employee->shift;
        if (!$shift) {
            return;
        }

        $shiftStart = Carbon::parse($attendance->date . ' ' . $shift->start_time);
        $shiftEnd = Carbon::parse($attendance->date . ' ' . $shift->end_time);
        
        // Calculate total minutes worked
        $totalWorkedMinutes = $timeIn->diffInMinutes($timeOut);
        
        // Subtract break time if exists
        if ($attendance->break_out && $attendance->break_in) {
            $breakOut = Carbon::parse($attendance->break_out);
            $breakIn = Carbon::parse($attendance->break_in);
            $breakMinutes = $breakOut->diffInMinutes($breakIn);
            $totalWorkedMinutes -= $breakMinutes;
        }

        // Calculate regular and overtime hours
        $regularMinutes = 0;
        $overtimeMinutes = 0;

        // If worked within shift hours
        if ($timeIn <= $shiftEnd && $timeOut >= $shiftStart) {
            $regularStart = max($timeIn, $shiftStart);
            $regularEnd = min($timeOut, $shiftEnd);
            $regularMinutes = $regularStart->diffInMinutes($regularEnd);
            
            // Subtract break time from regular hours if break is during shift
            if ($attendance->break_out && $attendance->break_in) {
                $breakOut = Carbon::parse($attendance->break_out);
                $breakIn = Carbon::parse($attendance->break_in);
                if ($breakOut >= $shiftStart && $breakIn <= $shiftEnd) {
                    $breakMinutes = $breakOut->diffInMinutes($breakIn);
                    $regularMinutes -= $breakMinutes;
                }
            }
        }

        // Calculate overtime
        $overtimeMinutes = $totalWorkedMinutes - $regularMinutes;

        // Update attendance record with correct column names
        $attendance->total_hours = round($totalWorkedMinutes / 60, 2);
        $attendance->regular_hours = round($regularMinutes / 60, 2);
        $attendance->overtime_hours = round($overtimeMinutes / 60, 2);
        $attendance->save();
    }

    /**
     * Get appropriate success message based on attendance state
     */
    private function getSuccessMessage($attendance)
    {
        if ($attendance->time_out) {
            return 'Attendance completed for the day.';
        } else {
            return 'Attendance logged successfully.';
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
        $attendance = Attendance::findOrFail($id);
        
        $validated = $request->validate([
            'date' => 'required|date',
            'time_in' => 'nullable',
            'break_out' => 'nullable',
            'break_in' => 'nullable',
            'time_out' => 'nullable',
            'status' => 'required|in:Present,Absent,Half Day,Leave,Partial',
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

            // Auto-mark as Absent if sequence is incomplete at end of day
            if ($validated['time_in'] && (!$validated['break_out'] || !$validated['break_in'] || !$validated['time_out'])) {
                $validated['status'] = 'Absent';
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
