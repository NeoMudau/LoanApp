<?php

namespace App\Command;

use App\Entity\Customer;
use App\Entity\EmploymentDetails;
use App\Entity\MonthlyExpense;
use App\Service\LoanAffordabilityService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-loan-affordability',
    description: 'Test the QuickLoan affordability calculations'
)]
class TestLoanAffordabilityCommand extends Command
{
    public function __construct(
        private LoanAffordabilityService $affordabilityService
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $io = new SymfonyStyle($input, $output);

        // Create sample customer
        $customer = new Customer();

        // Sample employment details
        $employment = new EmploymentDetails();
        $employment->setSalary(10000);

        $customer->setEmploymentDetails($employment);

        // Sample monthly expenses
        $expenses = [
            ['category' => 'Accommodation / Rent', 'amount' => 3000],
            ['category' => 'Transport', 'amount' => 1200],
            ['category' => 'Food and Groceries', 'amount' => 2000],
        ];

        foreach ($expenses as $expenseData) {

            $expense = new MonthlyExpense();

            $expense->setCategory($expenseData['category']);
            $expense->setAmount($expenseData['amount']);

            $customer->addMonthlyExpense($expense);
        }

        // Calculate affordability for six months
        $result = $this->affordabilityService->calculate($customer, 6);

        // Display results
        $io->title('QuickLoan Affordability Test');

        $io->table(
            ['Financial Information', 'Amount'],
            [
                ['Monthly Income', 'R ' . number_format($result['monthly_income'], 2)],
                ['Monthly Expenses', 'R ' . number_format($result['total_expenses'], 2)],
                ['Disposable Income', 'R ' . number_format($result['disposable_income'], 2)],
            ]
        );

        $io->section('Repayment Scenarios');

        $rows = [];

        foreach ($result['repayment_options'] as $option) {
            $rows[] = [
                $option['percentage'] . '%',
                'R ' . number_format($option['monthly_repayment'], 2),
                'R ' . number_format($option['total_repayment_capacity'], 2),
            ];
        }

        $io->table(
            ['Allocation', 'Monthly Capacity', '6-Month Capacity'],
            $rows
        );

        $io->success('Affordability calculation completed successfully.');

        return Command::SUCCESS;
    }
}
