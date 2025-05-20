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
        if ($contribution->calculation_type === 'fixed') {
            return $contribution->value;
        }

        return match ($contribution->contributionType->name) {
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
        return round($salary * 0.09, 2);
    }

    /**
     * Calculate PhilHealth contribution
     * PhilHealth contribution is based on salary brackets
     */
    private function calculatePhilHealth(float $salary): float
    {
        if ($salary <= 10000) {
            return 250.00; // Minimum employee share
        } elseif ($salary >= 100000) {
            return 2500.00; // Maximum employee share
        } else {
            return round($salary * 0.0225, 2); // 2.25% employee share
        }
    }

    /**
     * Calculate Pag-IBIG contribution
     * Pag-IBIG contribution is 1% for ₱1,500 and below, 2% for over ₱1,500
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