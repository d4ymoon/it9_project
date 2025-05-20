<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contribution;
use App\Models\Employee;
use App\Models\ContributionType;
use App\Services\ContributionCalculationService;

class ContributionSeeder extends Seeder
{
    public function run(): void
    {
        // Get all employees
        $employees = Employee::all();
        
        // Get contribution types
        $gsis = ContributionType::where('name', 'GSIS')->first();
        $philhealth = ContributionType::where('name', 'PhilHealth')->first();
        $pagibig = ContributionType::where('name', 'Pag-IBIG')->first();

        $contributionService = new ContributionCalculationService();

        foreach ($employees as $employee) {
            $monthlySalary = $employee->position->salary;

            // GSIS - 9% of salary
            Contribution::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'contribution_type_id' => $gsis->id,
                ],
                [
                    'calculation_type' => 'salary_based',
                    'value' => 9.00, // 9%
                ]
            );

            // PhilHealth - Based on salary brackets
            Contribution::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'contribution_type_id' => $philhealth->id,
                ],
                [
                    'calculation_type' => 'salary_based',
                    'value' => 2.25, // 2.25% employee share
                ]
            );

            // Pag-IBIG - 2% for salary over ₱1,500
            Contribution::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'contribution_type_id' => $pagibig->id,
                ],
                [
                    'calculation_type' => 'salary_based',
                    'value' => 2.00, // 2% for salary over ₱1,500
                ]
            );
        }
    }
} 