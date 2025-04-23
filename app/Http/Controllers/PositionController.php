<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Position;
use App\Models\Employee;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $positions = Position::withCount('employees')->get();
        return view('positions.index', compact( 'positions'));
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
            'name' => 'required|string|max:100|unique:positions,name',
            'salary' => 'required|numeric|min:0',
        ]);
    
        Position::create([
            'name' => $validated['name'],
            'salary' => $validated['salary'],
        ]);

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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
        ]);
    
        $position = Position::findOrFail($id);
    
        $oldSalary = $position->salary;
        $position->update($validated);
    
        $message = 'Position updated successfully.';
    
        if ($validated['salary'] != $oldSalary) {
            $affectedEmployees = \App\Models\Employee::where('position_id', $position->id)->get();
    
            foreach ($affectedEmployees as $employee) {
                $payroll = $employee->payroll;
                if ($payroll) {
                    $basicPay = $validated['salary'];
                    $tax = $this->calculateTax($basicPay);
                    $taxableIncome = $basicPay;
                    $netSalary = $taxableIncome - $tax;
    
                    $payroll->update([
                        'basic_pay' => $basicPay,
                        'taxable_income' => $taxableIncome,
                        'tax' => $tax,
                        'net_salary' => $netSalary,
                    ]);
                }
            }
    
            $message .= " {$affectedEmployees->count()} employee(s) salary affected.";
        }
    
        return redirect()
            ->route('positions.index')
            ->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $position = Position::findOrFail($id);

        $employeeCount = Employee::where('position_id', $id)->count();
    
        if ($employeeCount > 0) {
            return redirect()->route('positions.index')->with('error', 'This position is currently in use by an employee and cannot be deleted.');
        }
    
        $position->delete();
    
        return redirect()->route('positions.index')->with('success', 'Position deleted successfully.');
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
