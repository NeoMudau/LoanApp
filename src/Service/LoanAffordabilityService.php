<?php

namespace App\Service;

use App\Entity\Customer;

final class LoanAffordabilityService
{
    public function calculate(Customer $customer, int $termMonths): array
    {
        // Get monthly salary
        $employmentDetails = $customer->getEmploymentDetails();

        $monthlyIncome = $employmentDetails
            ? (float) $employmentDetails->getSalary()
            : 0.0;

        // Calculate total monthly expenses
        $totalExpenses = 0.0;

        foreach ($customer->getMonthlyExpenses() as $expense) {
            $totalExpenses += (float) $expense->getAmount();
        }

        // Calculate disposable income
        $disposableIncome = $monthlyIncome - $totalExpenses;

        // Only positive disposable income can support repayments
        $availableIncome = max(0, $disposableIncome);

        // Calculate repayment scenarios
        $repaymentOptions = [];

        foreach ([30, 40, 50] as $percentage) {

            $monthlyRepayment = $availableIncome * ($percentage / 100);

            $repaymentOptions[$percentage] = [
                'percentage' => $percentage,
                'monthly_repayment' => round($monthlyRepayment, 2),
                'total_repayment_capacity' => round(
                    $monthlyRepayment * $termMonths,
                    2
                ),
            ];
        }

        return [
            'monthly_income' => round($monthlyIncome, 2),
            'total_expenses' => round($totalExpenses, 2),
            'disposable_income' => round($disposableIncome, 2),
            'repayment_options' => $repaymentOptions,
            'term_months' => $termMonths,
        ];
    }
}
