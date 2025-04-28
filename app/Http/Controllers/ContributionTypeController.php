<?php

namespace App\Http\Controllers;

use App\Models\ContributionType;
use Illuminate\Http\Request;

class ContributionTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $contributionTypes = ContributionType::all();
        return view('contribution.index', compact('contributionTypes'));
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
        $request->validate([
            'name' => 'required|string|max:255|unique:contribution_types,name',
        ]);

        ContributionType::create([
            'name' => $request->name,
        ]);

        return back()->with('success', 'Contribution type added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ContributionType $contribution_Type)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContributionType $contribution_Type)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContributionType $id)
    {
        //
        $type = ContributionType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:contribution_types,name,' . $type->id,
        ]);

        $type->update([
            'name' => $request->name,
        ]);

        return back()->with('success', 'Contribution type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContributionType $id)
    {
        //
        $type = ContributionType::findOrFail($id);
        $type->delete();

        return back()->with('success', 'Contribution type deleted successfully.');
    }
}
