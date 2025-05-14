<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;
use App\Models\Shift;
use App\Models\Employee;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class EmployeeAndAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Get the existing Morning Shift
        $shift = Shift::where('name', 'Morning')->firstOrFail();

        // 2. Get or Create Staff Position
        $position = Position::firstOrCreate(
            ['name' => 'Staff'],
            ['salary' => 10000.00]
        );

        // 3. Create 3 Employees with their Users
        $employees = [
            [
                'name' => 'Alice Smith',
                'email' => 'alice.smith@company.com',
                'contact_number' => '09123456789',
                'attendance_pattern' => 'punctual' // Always on time
            ],
            [
                'name' => 'Bob Johnson',
                'email' => 'bob.johnson@company.com',
                'contact_number' => '09234567890',
                'attendance_pattern' => 'overtime' // Does overtime
            ],
            [
                'name' => 'Carol Williams',
                'email' => 'carol.williams@company.com',
                'contact_number' => '09345678901',
                'attendance_pattern' => 'late' // Sometimes late
            ]
        ];

        foreach ($employees as $employeeData) {
            // Create Employee
            $employee = Employee::create([
                'name' => $employeeData['name'],
                'email' => $employeeData['email'],
                'contact_number' => $employeeData['contact_number'],
                'position_id' => $position->id,
                'shift_id' => $shift->id,
                'hire_date' => '2025-01-01',
                'payment_method' => $employeeData['name'] === 'Alice Smith' ? 'bank' : 'cash',
                'bank_name' => $employeeData['name'] === 'Alice Smith' ? 'Metro Bank' : null,
                'bank_acct' => $employeeData['name'] === 'Alice Smith' ? '555555555' : null,
                'status' => 'active'
            ]);

            // Create User account for the employee
            $user = User::create([
                'name' => $employee->name,
                'email' => $employee->email,
                'password' => Hash::make('password'),
                'role' => 'employee',
                'employee_id' => $employee->id,
            ]);

            // Link the user to the employee
            $employee->user_id = $user->id;
            $employee->save();

            // Generate Attendance Records (March-April 2025)
            $startDate = Carbon::create(2025, 3, 1);
            $endDate = Carbon::create(2025, 4, 30);

            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                // Skip weekends
                if ($date->isWeekend()) {
                    continue;
                }

                $timeIn = null;
                $breakOut = null;
                $breakIn = null;
                $timeOut = null;
                $status = 'Present';

                // Base times
                $shiftStart = Carbon::parse($date->format('Y-m-d') . ' ' . $shift->start_time);
                $breakStart = Carbon::parse($date->format('Y-m-d') . ' ' . $shift->break_start_time);
                $breakEnd = Carbon::parse($date->format('Y-m-d') . ' ' . $shift->break_end_time);
                $shiftEnd = Carbon::parse($date->format('Y-m-d') . ' ' . $shift->end_time);

                // Random variation in minutes (-30 to +30)
                $randomVariation = rand(-30, 30);

                switch ($employeeData['attendance_pattern']) {
                    case 'punctual':
                        // Always on time with small variations
                        $timeIn = $shiftStart->copy()->addMinutes(rand(-5, 5));
                        $breakOut = $breakStart->copy()->addMinutes(rand(-5, 5));
                        $breakIn = $breakEnd->copy()->addMinutes(rand(-5, 5));
                        $timeOut = $shiftEnd->copy()->addMinutes(rand(-5, 5));
                        break;

                    case 'overtime':
                        // Comes on time but stays late
                        $timeIn = $shiftStart->copy()->addMinutes(rand(-10, 10));
                        $breakOut = $breakStart->copy()->addMinutes(rand(-5, 5));
                        $breakIn = $breakEnd->copy()->addMinutes(rand(-5, 5));
                        $timeOut = $shiftEnd->copy()->addMinutes(rand(30, 120)); // 30-120 minutes overtime
                        break;

                    case 'late':
                        // Sometimes late
                        if (rand(1, 100) <= 30) { // 30% chance of being late
                            $timeIn = $shiftStart->copy()->addMinutes(rand(15, 45));
                            $status = 'Late';
                        } else {
                            $timeIn = $shiftStart->copy()->addMinutes(rand(-5, 5));
                        }
                        $breakOut = $breakStart->copy()->addMinutes(rand(-5, 5));
                        $breakIn = $breakEnd->copy()->addMinutes(rand(-5, 5));
                        $timeOut = $shiftEnd->copy()->addMinutes(rand(-10, 10));
                        break;
                }

                // Create attendance record
                Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $date->format('Y-m-d'),
                    'time_in' => $timeIn,
                    'break_out' => $breakOut,
                    'break_in' => $breakIn,
                    'time_out' => $timeOut,
                    'status' => $status
                ]);
            }
        }
    }
} 