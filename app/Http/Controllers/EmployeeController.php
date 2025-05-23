<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee; 
use App\Models\Position;
use App\Models\Contribution;
use App\Models\ContributionType;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {   
        $query = Employee::with(['position', 'contributions.contributionType', 'shift']);

        // Search by employee name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
        }

        $employees = $query->latest()->paginate(10);
        $positions = Position::all();
        $contributionTypes = ContributionType::all();
        $shifts = Shift::all();

        return view('employees.index', compact('employees', 'positions', 'contributionTypes', 'shifts'));
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:11',
            'email' => 'required|email|unique:employees,email',
            'position_id' => 'required|exists:positions,id',
            'hire_date' => 'required|date',
            'bank_acct' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,bank',
            'shift_id' => 'required|exists:shifts,id',
        ]);

        // Set default status to active
        $validated['status'] = 'active';

        DB::beginTransaction();
        try {
            // Create employee
            $employee = Employee::create($validated);

            // Create user account
            $user = User::create([
                'name' => $employee->name,
                'email' => $employee->email,
                'password' => bcrypt('password'),
                'role' => 'employee',
                'employee_id' => $employee->id
            ]);

            // Link user to employee
            $employee->update(['user_id' => $user->id]);

            // Get contribution types
            $gsis = ContributionType::where('name', 'GSIS')->first();
            $philhealth = ContributionType::where('name', 'PhilHealth')->first();
            $pagibig = ContributionType::where('name', 'Pag-IBIG')->first();

            // Create contributions for the employee
            $contributions = [
                [
                    'employee_id' => $employee->id,
                    'contribution_type_id' => $gsis->id,
                    'calculation_type' => 'salary_based',
                    'value' => 9.00, // 9%
                ],
                [
                    'employee_id' => $employee->id,
                    'contribution_type_id' => $philhealth->id,
                    'calculation_type' => 'salary_based',
                    'value' => 2.25, // 2.25% employee share
                ],
                [
                    'employee_id' => $employee->id,
                    'contribution_type_id' => $pagibig->id,
                    'calculation_type' => 'salary_based',
                    'value' => 2.00, // 2% for salary over ₱1,500
                ],
            ];

            foreach ($contributions as $contribution) {
                Contribution::create($contribution);
            }

            DB::commit();
            return redirect()->route('employees.index')->with('success', 'Employee created successfully with contributions.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error creating employee: ' . $e->getMessage());
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
        $employee = Employee::find($id);

        $employee->name = $request->name;
        $employee->contact_number = $request->contact_number;
        $employee->email = $request->email;
        $employee->bank_acct = $request->bank_acct;
        $employee->bank_name = $request->bank_name;
        $employee->payment_method = $request->payment_method;
        $employee->shift_id = $request->shift_id;
    
        // Save the updated employee record
        $employee->save();
    
        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $employee = Employee::findOrFail($id);
        
        // Delete associated user if exists
        if ($employee->user) {
            $employee->user->delete();
        }
        
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee and associated user account deleted successfully.');
    }

    public function updateRole(Request $request, Employee $employee)
{
    $request->validate([
        'role' => 'required|in:employee,admin,hr',
    ]);

    // Make sure employee has a user
    if (!$employee->user) {
        return redirect()->back()->with('error', 'No user account linked to this employee.');
    }

    // Only admins can change roles
        $currentUser = auth()->user();
        if (!$currentUser || $currentUser->role !== 'admin') {
        abort(403);
    }

    $employee->user->role = $request->input('role');
    $employee->user->save();

    return redirect()->back()->with('success', 'User role updated successfully.');
}

    private function calculateTax($basicPay)
    {
        if ($basicPay <= 10417) {
            return 0;
        } elseif ($basicPay <= 16666) {
            return 0 + 0.15 * ($basicPay - 10417);
        } elseif ($basicPay <= 33332) {
            return 1250 + 0.20 * ($basicPay - 16667);
        } elseif ($basicPay <= 83332) {
            return 5416.67 + 0.25 * ($basicPay - 33333);
        } elseif ($basicPay <= 333332) {
            return 20416.67 + 0.30 * ($basicPay - 83333);
        } else {
            return 100416.67 + 0.35 * ($basicPay - 333333);
        }
    }
}
