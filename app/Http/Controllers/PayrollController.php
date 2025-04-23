<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Models\DeductionType;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $payrolls = Payroll::with(['employee', 'deductions'])->get();
        $deductionTypes = DeductionType::all();
        
        return view('payrolls.index', compact('payrolls', 'deductionTypes'));
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
        $payroll = Payroll::with(['employee', 'deductions.deductionType'])->findOrFail($id);
    $deductionTypes = DeductionType::all();

    return view('payrolls.edit', compact('payroll', 'deductionTypes'));
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $payroll = Payroll::find($id);

        // Update the payroll data
     
        $payroll->overtime_pay = $request->overtime_pay;
  
        $payroll->pay_period = $request->pay_period;
    
        // Save the updated payroll
        $payroll->save();
    
        return redirect()->route('payrolls.edit', $payroll->id)->with('success', 'Payroll updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
