<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Payroll; 
use App\Models\Position;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $positions = Position::all();
        $employees = Employee::with('payroll')->get();
        return view('employees.index', compact('employees', 'positions'));
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:11',
            'email' => 'required|email|unique:employees,email',
            'position_id' => 'required|exists:positions,id',
            'hire_date' => 'required|date',
            'bank_acct' => 'required|string|max:255',
        ]);
    
        $employee = Employee::create($validated);

        $positionSalary = \App\Models\Position::find($validated['position_id'])->salary;
        $tax = $this->calculateTax($positionSalary);
        $taxableIncome = $positionSalary; // no deductions yet, 
        $netSalary = $taxableIncome - $tax;

        $payroll = new Payroll();
        $payroll->employee_id = $employee->id;
        $payroll->basic_pay = $positionSalary;
        $payroll->overtime_pay = 0;
        $payroll->total_deductions = 0;
        $payroll->taxable_income = $taxableIncome;
        $payroll->tax = $tax;
        $payroll->net_salary = $netSalary;
        $payroll->pay_period = null;
        $payroll->save();
    
    
        return redirect()->route('employees.index');
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
    
        // Save the updated payroll
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
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
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
