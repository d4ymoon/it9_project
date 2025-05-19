<?php

namespace App\Services;

use App\Models\Contribution;
use App\Models\ContributionType;

class ContributionCalculationService
{
    /**
     * Calculate the contribution amount based on the employee's salary
     *
     * @param float $salary The employee's salary
     * @param Contribution $contribution The contribution record
     * @return float The calculated contribution amount
     */
    public function calculateContribution(float $salary, Contribution $contribution): float
    {
        $type = $contribution->contributionType->name;

        return match ($type) {
            'GSIS' => $this->calculateGSIS($salary),
            'PhilHealth' => $this->calculatePhilHealth($salary),
            'Pag-IBIG' => $this->calculatePagIBIG($salary),
            default => 0.00
        };
    }

    /**
     * Calculate GSIS contribution
     * GSIS contribution is 9% of the monthly salary
     */
    private function calculateGSIS(float $salary): float
    {
        return $salary * 0.09;
    }

    /**
     * Calculate PhilHealth contribution
     * PhilHealth contribution is 3% of the monthly salary
     */
   private function calculatePhilHealth(float $salary): float
    {
        $rate = 0.045; // Total 4.5%
        $employeeShareRate = $rate / 2; // 2.25%

        if ($salary <= 10000) {
            return 250.00; // Minimum employee share
        } elseif ($salary >= 100000) {
            return 2500.00; // Maximum employee share
        } else {
            return round($salary * $employeeShareRate, 2);
        }
    }
    /**
     * Calculate Pag-IBIG contribution
     * Pag-IBIG contribution is 2% of the monthly salary
     */
    private function calculatePagIBIG(float $salary): float
    {
        if ($salary <= 1500) {
            return round($salary * 0.01, 2); // 1% for ₱1,500 and below
        } else {
            return round($salary * 0.02, 2); // 2% for over ₱1,500
        }
    }
} 