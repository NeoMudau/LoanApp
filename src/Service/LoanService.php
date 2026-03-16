<?php

namespace App\Service;

class LoanService
{
    public function getTotalLoans(): int
    {
        return $this->loanRepository->countLoans();
    }

    public function getActiveLoanCount(): int
    {
        return $this->loanRepository->countActiveLoans();
    }

    public function getClosedLoanCount(): int
    {
        return $this->loanRepository->countClosedLoans();
    }

    public function getOverdueLoanCount(): int
    {
        return $this->loanRepository->countOverdueLoans();
    }

    public function getTodayLoanCount(): int
    {
        return $this->loanRepository->countLoansCreatedToday();
    }

    public function getMonthLoanCount(): int
    {
        return $this->loanRepository->countLoansCreatedThisMonth();
    }

    public function getLoanAverage(): float
    {
        return $this->loanRepository->averageLoanAmount();
    }

    public function getAverageInterestRate(): float
    {
        return $this->loanRepository->averageInterestPercent();
    }

    public function getAverageLoanTerm(): float
    {
        return $this->loanRepository->averageLoanTermMonths();
    }

    public function getTotalPrincipalIssued(): float
    {
        return $this->loanRepository->sumLoanPrincipal();
    }

    public function getActivePrincipalTotal(): float
    {
        return $this->loanRepository->sumActiveLoanPrincipal();
    }

    public function getTotalAmountCollected(): float
    {
        return $this->loanRepository->sumTotalPayback();
    }

    public function getTotalInterestCollected(): float
    {
        return $this->loanRepository->sumInterestCollected();
    }

    public function getTotalBalanceRemaining(): float
    {
        return $this->loanRepository->sumBalanceRemaining();
    }

    public function getPortfolioValue(): float
    {
        return $this->loanRepository->getTotalPortfolioValue();
    }

    public function getPortfolioAtRisk(): float
    {
        return $this->loanRepository->getPortfolioAtRisk();
    }

    public function getLargestLoan()
    {
        return $this->loanRepository->findLargestLoan();
    }

    public function getSmallestLoan()
    {
        return $this->loanRepository->findSmallestLoan();
    }

    public function getLatestLoan()
    {
        return $this->loanRepository->findLatestLoan();
    }

    public function getOldestLoan()
    {
        return $this->loanRepository->findOldestLoan();
    }

    public function getLoansClosingSoon(int $days): array
    {
        return $this->loanRepository->findLoansClosingSoon($days);
    }

    public function getOverdueLoans(): array
    {
        return $this->loanRepository->findOverdueLoans();
    }

    public function getActiveLoans(): array
    {
        return $this->loanRepository->findActiveLoans();
    }

    public function getClosedLoans(): array
    {
        return $this->loanRepository->findClosedLoans();
    }

    public function getLoansWithExtensions(): array
    {
        return $this->loanRepository->findLoansWithExtensions();
    }

    public function getExtensionRate(): float
    {
        $total = $this->loanRepository->countLoans();
        $extensions = $this->loanRepository->countLoansWithExtensions();

        if ($total === 0) {
            return 0;
        }

        return ($extensions / $total) * 100;
    }

    public function getLoanStatusDistribution(): array
    {
        return $this->loanRepository->getLoanStatusDistribution();
    }

    public function getLoanTermDistribution(): array
    {
        return $this->loanRepository->getLoanTermDistribution();
    }

    public function getInterestRateDistribution(): array
    {
        return $this->loanRepository->getInterestRateDistribution();
    }

    public function getAdminKpiSummary(): array
    {
        return [
            'total_loans' => $this->loanRepository->countLoans(),
            'active_loans' => $this->loanRepository->countActiveLoans(),
            'overdue_loans' => $this->loanRepository->countOverdueLoans(),
            'principal_issued' => $this->loanRepository->sumLoanPrincipal(),
            'balance_remaining' => $this->loanRepository->sumBalanceRemaining(),
            'interest_collected' => $this->loanRepository->sumInterestCollected(),
            'portfolio_at_risk' => $this->loanRepository->getPortfolioAtRisk()
        ];
    }

    public function getReportsSummary(): array
    {
        return [
            'total_loans' => $this->loanRepository->countLoans(),
            'active_loans' => $this->loanRepository->countActiveLoans(),
            'closed_loans' => $this->loanRepository->countClosedLoans(),
            'overdue_loans' => $this->loanRepository->countOverdueLoans(),
            'loans_this_month' => $this->loanRepository->countLoansCreatedThisMonth(),
            'loans_today' => $this->loanRepository->countLoansCreatedToday(),
            'principal_issued' => $this->loanRepository->sumLoanPrincipal(),
            'active_principal' => $this->loanRepository->sumActiveLoanPrincipal(),
            'balance_remaining' => $this->loanRepository->sumBalanceRemaining(),
            'interest_total' => $this->loanRepository->sumInterestAmount(),
            'interest_collected' => $this->loanRepository->sumInterestCollected(),
            'total_payback' => $this->loanRepository->sumTotalPayback(),
            'average_loan' => $this->loanRepository->averageLoanAmount(),
            'average_interest' => $this->loanRepository->averageInterestPercent(),
            'average_term' => $this->loanRepository->averageLoanTermMonths(),
            'portfolio_value' => $this->loanRepository->getTotalPortfolioValue(),
            'portfolio_risk' => $this->loanRepository->getPortfolioAtRisk(),
            'status_distribution' => $this->loanRepository->getLoanStatusDistribution(),
            'term_distribution' => $this->loanRepository->getLoanTermDistribution(),
            'interest_distribution' => $this->loanRepository->getInterestRateDistribution()
        ];
    }

    public function getCustomerDashboardSummary(int $customerId): array
    {
        return [
            'total_loans' => $this->loanRepository->countLoansByCustomer($customerId),
            'active_loans' => $this->loanRepository->countActiveLoansByCustomer($customerId),
            'closed_loans' => $this->loanRepository->countClosedLoansByCustomer($customerId),
            'total_borrowed' => $this->loanRepository->sumLoanAmountByCustomer($customerId),
            'balance_remaining' => $this->loanRepository->sumBalanceByCustomer($customerId),
            'interest_total' => $this->loanRepository->sumInterestByCustomer($customerId),
            'total_paid' => $this->loanRepository->sumCollectedByCustomer($customerId)
        ];
    }
}