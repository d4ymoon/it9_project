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
            'date' => 'required|date',
            'time_in' => 'nullable',
            'break_out' => 'nullable',
            'break_in' => 'nullable',
            'time_out' => 'nullable',
            'status' => 'required|in:Present,Absent,Half Day,Leave',
        ]);

        // If status is Leave, we don't need time entries
        if ($validated['status'] === 'Leave') {
            $validated['time_in'] = null;
            $validated['break_out'] = null;
            $validated['break_in'] = null;
            $validated['time_out'] = null;
        } else {
            // Combine date with time fields if they exist
            $fields = ['time_in', 'break_out', 'break_in', 'time_out'];
            foreach ($fields as $field) {
                if (!empty($validated[$field])) {
                    $validated[$field] = $validated['date'] . ' ' . $validated[$field];
                }
            }
        }

        Attendance::create($validated);

        return redirect()->route('attendances.index')
            ->with('success', 'Attendance record added successfully');
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

        // If status is Leave, we don't need time entries
        if ($validated['status'] === 'Leave') {
            $validated['time_in'] = null;
            $validated['break_out'] = null;
            $validated['break_in'] = null;
            $validated['time_out'] = null;
        } else {
            // Combine date with time fields
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
        //
    }
}
