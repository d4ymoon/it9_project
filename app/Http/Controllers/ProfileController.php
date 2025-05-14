<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Employee;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $employee = $user->employee;
        return view('profile.edit', compact('user', 'employee'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:11',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update user name
        $user->name = $request->name;
        
        // Update employee details
        if ($employee) {
            $employee->name = $request->name;
            $employee->contact_number = $request->contact_number;
            $employee->save();
        }

        // Handle password update if provided
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The current password is incorrect.']);
            }

            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }
} 