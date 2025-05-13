<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Employee;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $loans = Loan::with('employee')->latest()->get();
        $employees = Employee::all();
        return view('loans.index', compact('loans', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::all();
        return view('loans.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'loan_type' => 'required|string|max:255',
            'loan_amount' => 'required|numeric|min:0',
            'deduction_percentage' => 'required|numeric|min:1|max:50', // Maximum 50% of salary can be deducted
            'start_date' => 'required|date',
        ]);

        $validated['remaining_balance'] = $validated['loan_amount'];
        
        Loan::create($validated);

        return redirect()->route('loans.index')
            ->with('success', 'Loan created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Loan $loan)
    {
        return view('loans.show', compact('loan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Loan $loan)
    {
        $employees = Employee::all();
        return view('loans.edit', compact('loan', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'loan_type' => 'required|string|max:255',
            'loan_amount' => 'required|numeric|min:0',
            'deduction_percentage' => 'required|numeric|min:1|max:50', // Maximum 50% of salary can be deducted
            'start_date' => 'required|date',
            'status' => 'required|in:active,paid,cancelled'
        ]);

        $loan->update($validated);

        return redirect()->route('loans.index')
            ->with('success', 'Loan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loan $loan)
    {
        $loan->delete();

        return redirect()->route('loans.index')
            ->with('success', 'Loan deleted successfully.');
    }
}
