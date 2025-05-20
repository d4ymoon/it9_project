<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;
use App\Models\Shift;
use App\Models\Employee;
use App\Models\User;
use App\Models\Attendance;
use App\Models\ContributionType;
use App\Models\Contribution;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class EmployeeAndAttendanceSeeder2 extends Seeder
{
    public function run(): void
    {
        // 1. Get the existing Morning Shift
        $shift = Shift::where('name', 'Morning')->firstOrFail();

        // 2. Create new positions
        $positions = [
            [
                'name' => 'Senior Developer',
                'salary' => 80000.00
            ],
            [
                'name' => 'HR Manager',
                'salary' => 65000.00
            ],
            [
                'name' => 'Marketing Specialist',
                'salary' => 45000.00
            ]
        ];

        $createdPositions = [];
        foreach ($positions as $positionData) {
            $createdPositions[] = Position::create($positionData);
        }

        // 3. Get contribution types
        $gsisType = ContributionType::where('name', 'GSIS')->first();
        $philhealthType = ContributionType::where('name', 'PhilHealth')->first();
        $pagibigType = ContributionType::where('name', 'Pag-IBIG')->first();

        // 4. Create 15 Employees with their Users
        $employees = [
            // Senior Developers
            [
                'name' => 'Michael Chen',
                'email' => 'michael.chen@company.com',
                'contact_number' => '09111222333',
                'position_id' => $createdPositions[0]->id,
                'attendance_pattern' => 'overtime'
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@company.com',
                'contact_number' => '09222333444',
                'position_id' => $createdPositions[0]->id,
                'attendance_pattern' => 'punctual'
            ],
            [
                'name' => 'David Kim',
                'email' => 'david.kim@company.com',
                'contact_number' => '09333444555',
                'position_id' => $createdPositions[0]->id,
                'attendance_pattern' => 'overtime'
            ],
            
            // HR Managers
            [
                'name' => 'Emily Rodriguez',
                'email' => 'emily.rodriguez@company.com',
                'contact_number' => '09444555666',
                'position_id' => $createdPositions[1]->id,
                'attendance_pattern' => 'punctual'
            ],
            [
                'name' => 'James Wilson',
                'email' => 'james.wilson@company.com',
                'contact_number' => '09555666777',
                'position_id' => $createdPositions[1]->id,
                'attendance_pattern' => 'late'
            ],
            [
                'name' => 'Maria Garcia',
                'email' => 'maria.garcia@company.com',
                'contact_number' => '09666777888',
                'position_id' => $createdPositions[1]->id,
                'attendance_pattern' => 'punctual'
            ],
            [
                'name' => 'Robert Lee',
                'email' => 'robert.lee@company.com',
                'contact_number' => '09777888999',
                'position_id' => $createdPositions[1]->id,
                'attendance_pattern' => 'overtime'
            ],
            [
                'name' => 'Lisa Chen',
                'email' => 'lisa.chen@company.com',
                'contact_number' => '09888999000',
                'position_id' => $createdPositions[1]->id,
                'attendance_pattern' => 'late'
            ],
            
            // Marketing Specialists
            [
                'name' => 'John Martinez',
                'email' => 'john.martinez@company.com',
                'contact_number' => '09999000111',
                'position_id' => $createdPositions[2]->id,
                'attendance_pattern' => 'punctual'
            ],
            [
                'name' => 'Anna Kim',
                'email' => 'anna.kim@company.com',
                'contact_number' => '09000111222',
                'position_id' => $createdPositions[2]->id,
                'attendance_pattern' => 'overtime'
            ],
            [
                'name' => 'Thomas Wang',
                'email' => 'thomas.wang@company.com',
                'contact_number' => '09111222333',
                'position_id' => $createdPositions[2]->id,
                'attendance_pattern' => 'late'
            ],
            [
                'name' => 'Sofia Santos',
                'email' => 'sofia.santos@company.com',
                'contact_number' => '09222333444',
                'position_id' => $createdPositions[2]->id,
                'attendance_pattern' => 'punctual'
            ],
            [
                'name' => 'Kevin Park',
                'email' => 'kevin.park@company.com',
                'contact_number' => '09333444555',
                'position_id' => $createdPositions[2]->id,
                'attendance_pattern' => 'overtime'
            ],
            [
                'name' => 'Rachel Wong',
                'email' => 'rachel.wong@company.com',
                'contact_number' => '09444555666',
                'position_id' => $createdPositions[2]->id,
                'attendance_pattern' => 'late'
            ],
            [
                'name' => 'Daniel Lee',
                'email' => 'daniel.lee@company.com',
                'contact_number' => '09555666777',
                'position_id' => $createdPositions[2]->id,
                'attendance_pattern' => 'overtime'
            ]
        ];

        foreach ($employees as $employeeData) {
            // Create Employee
            $employee = Employee::create([
                'name' => $employeeData['name'],
                'email' => $employeeData['email'],
                'contact_number' => $employeeData['contact_number'],
                'position_id' => $employeeData['position_id'],
                'shift_id' => $shift->id,
                'hire_date' => '2025-01-15',
                'payment_method' => rand(0, 1) ? 'bank' : 'cash',
                'bank_name' => rand(0, 1) ? 'BDO' : 'BPI',
                'bank_acct' => rand(0, 1) ? '1234567890' : '0987654321',
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

            // Create mandatory contributions for all employees
            // GSIS Contribution
            Contribution::create([
                'employee_id' => $employee->id,
                'contribution_type_id' => $gsisType->id,
                'calculation_type' => 'salary_based',
                'value' => 0 // Default value, will be calculated during payslip generation
            ]);

            // PhilHealth Contribution
            Contribution::create([
                'employee_id' => $employee->id,
                'contribution_type_id' => $philhealthType->id,
                'calculation_type' => 'salary_based',
                'value' => 0 // Default value, will be calculated during payslip generation
            ]);

            // Pag-IBIG Contribution
            Contribution::create([
                'employee_id' => $employee->id,
                'contribution_type_id' => $pagibigType->id,
                'calculation_type' => 'salary_based',
                'value' => 0 // Default value, will be calculated during payslip generation
            ]);

            // Generate Attendance Records (February-April 2025)
            $startDate = Carbon::create(2025, 2, 1);
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
                        // 1-3 hours overtime
                        $timeOut = $shiftEnd->copy()->addMinutes(rand(60, 180));
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
                $attendance = Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $date->format('Y-m-d'),
                    'time_in' => $timeIn,
                    'break_out' => $breakOut,
                    'break_in' => $breakIn,
                    'time_out' => $timeOut,
                    'status' => $status
                ]);

                // Calculate hours
                if ($timeIn && $timeOut) {
                    $totalHours = 0;
                    $regularHours = 0;
                    $overtimeHours = 0;

                    // Calculate total hours worked
                    $totalHours = Carbon::parse($timeIn)->diffInMinutes(Carbon::parse($timeOut)) / 60;
                    
                    // Subtract break time if break was taken
                    if ($breakOut && $breakIn) {
                        $breakDuration = Carbon::parse($breakOut)->diffInMinutes(Carbon::parse($breakIn)) / 60;
                        $totalHours -= $breakDuration;
                    }

                    // Calculate regular and overtime hours
                    $regularHours = min($totalHours, 8); // Standard 8-hour workday
                    $overtimeHours = max(0, $totalHours - 8);

                    // Update attendance record with calculated hours
                    $attendance->update([
                        'total_hours' => round($totalHours, 2),
                        'regular_hours' => round($regularHours, 2),
                        'overtime_hours' => round($overtimeHours, 2)
                    ]);
                }
            }
        }
    }
} 