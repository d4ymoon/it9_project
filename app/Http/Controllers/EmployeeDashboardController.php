<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Payslip;
use App\Models\Loan;
use App\Models\Contribution;
use Illuminate\Support\Facades\Hash;

class EmployeeDashboardController extends Controller
{
    public function dashboard()
    {
        return view('employee.dashboard');
    }

    public function attendances()
    {
        $employee = Auth::user()->employee;
        $attendances = Attendance::where('employee_id', $employee->id)
            ->orderBy('date', 'desc')
            ->paginate(10);
        
        return view('employee.attendances', compact('attendances'));
    }

    public function payslips()
    {
        $employee = Auth::user()->employee;
        $payslips = Payslip::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('employee.payslips', compact('payslips'));
    }

    public function loans()
    {
        $employee = Auth::user()->employee;
        $loans = Loan::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('employee.loans', compact('loans'));
    }

    public function contributions()
    {
        $employee = Auth::user()->employee;
        $contributions = Contribution::where('employee_id', $employee->id)
            ->with('contributionType')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('employee.contributions', compact('contributions'));
    }

    public function showChangePasswordForm()
    {
        return view('employee.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('employee.dashboard')->with('success', 'Password changed successfully.');
    }
} 