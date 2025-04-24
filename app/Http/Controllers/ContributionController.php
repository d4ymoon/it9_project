<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use Illuminate\Http\Request;

class ContributionController extends Controller
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
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'contribution_type_id' => 'required|exists:contribution_types,id',
            'calculation_type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
        ]);
    
        try {
            Contribution::create($request->all());
            return back()->with('success', 'Contribution added successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                // Duplicate entry error
                return back()->with('error', 'This employee already has this contribution type.');
            }
    
            // Other DB errors
            return back()->with('error', 'An error occurred while adding the contribution.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Contribution $contribution)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contribution $contribution)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contribution $id)
    {
        //
        $contribution = Contribution::findOrFail($id);

        $request->validate([
            'value' => 'required|numeric|min:0',
            'calculation_type' => 'required|in:fixed,percent',
        ]);

        $contribution->update([
            'value' => $request->value,
            'calculation_type' => $request->calculation_type,
        ]);

        return back()->with('success', 'Contribution updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contribution $id)
    {
        $contribution = Contribution::findOrFail($id);
        $contribution->delete();

        return back()->with('success', 'Contribution deleted successfully.');
    }
}
