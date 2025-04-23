<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deduction;

class DeductionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $validated =$request->validate([
            'payroll_id' => 'required|exists:payrolls,id',
            'deduction_type_id' => 'required|exists:deduction_types,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $exists = Deduction::where('payroll_id', $validated['payroll_id'])
                ->where('deduction_type_id', $validated['deduction_type_id'])
                ->exists();

    if ($exists) {
        return redirect()->back()
            ->withErrors(['deduction_type_id' => 'This deduction type already exists for this payroll.'])
            ->withInput()
            ->with('open_deduction_modal', true); 
    }
   
    $deduction = Deduction::create($validated);

    $deduction->payroll->recalculateTotalDeductions();
    

    return redirect()->route('payrolls.edit', $deduction->payroll->id)
        ->with('deduction_id', $deduction->id);
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
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);
    
        $deduction = Deduction::findOrFail($id);
        $deduction->update([
            'amount' => $request->amount,
        ]);
    
        $deduction->payroll->recalculateTotalDeductions();

        return redirect()->route('payrolls.edit', $deduction->payroll->id)
        ->with('deduction_id', $deduction->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function updateAll(Request $request)
    {
        $deductions = $request->input('deductions'); // This will be an associative array with deduction IDs as keys
    
        foreach ($deductions as $id => $amount) {
            $deduction = Deduction::find($id);
            if ($deduction) {
                $deduction->update(['amount' => $amount]);
            }
        }
    
        return redirect()->route('payrolls.edit')->with('success', 'All deductions updated successfully!');
    }

    public function destroy(string $id)
    {
        //
        $deduction = Deduction::findOrFail($id);
        $payroll = $deduction->payroll;
        $deduction->delete();

        $payroll->recalculateTotalDeductions();


    return redirect()->back()->with('success', 'Deduction deleted successfully.');
    }
}
