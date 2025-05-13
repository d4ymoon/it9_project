<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Shift;
use App\Models\Position;
use App\Models\Employee;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Morning Shift
        $shift = Shift::firstOrCreate([
            'name' => 'Morning'
        ], [
            'start_time' => '08:00:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00',
            'end_time' => '17:00:00',
            'description' => 'Regular morning shift',
            'is_active' => true,
        ]);

        // 2. Create Manager Position
        $position = Position::firstOrCreate([
            'name' => 'Manager'
        ], [
            'salary' => 10000.00,
        ]);

        // 3. Create John Doe Employee
        $employee = Employee::firstOrCreate([
            'email' => 'johndoe@email.com'
        ], [
            'name' => 'John Doe',
            'contact_number' => '00000000000',
            'position_id' => $position->id,
            'shift_id' => $shift->id,
            'hire_date' => now(),
            'status' => 'active',
        ]);

        // 4. Create User for John Doe
        $user = User::firstOrCreate([
            'email' => $employee->email,
        ], [
            'name' => $employee->name,
            'password' => Hash::make('password'),
            'role' => 'admin',
            'employee_id' => $employee->id,
        ]);

        // 5. Assign the User to Employee manually by setting the user_id
        $employee->user_id = $user->id;
        $employee->save();

        // 6. Ensure the user has employee_id set (in case firstOrCreate found existing user)
        if (!$user->employee_id) {
            $user->employee_id = $employee->id;
            $user->save();
        }
    }
}

