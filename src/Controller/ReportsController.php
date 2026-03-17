<?php

namespace App\Controller;

use App\Service\LoanService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

final class ReportsController extends AbstractController
{
    private LoanService $loanService;

    public function __construct(
        LoanService  $loanService,
    )
    {
        $this->loanService = $loanService;
    }
    
    #[Route('/reports', name: 'app_reports')]
    public function index(): Response
    {
        return $this->render('reports/index.html.twig', [
            'controller_name' => 'ReportsController',
        ]);
    }

    #[Route('/reports/overdue', name: 'overdue_loans')]
    public function generateOverdueLoansReport(): Response
    {
        return $this->render('reports/overdue.html.twig', [
            'controller_name' => 'ReportsController',
        ]);
    }

    #[Route('/monthly/report', name: 'monthly_report')]
    public function generateMonthlyReport(ChartBuilderInterface $chartBuilder): Response
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart2 = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);

        $reportSummary = $this->loanService->getReportsSummary();

        $months = ['Jan','Feb','Mar','Apr','May','Jun'];
        $loansIssued = [];
        $interestPaid = [];

        foreach ($months as $index => $monthName) {
            $loansIssued[] = $this->loanService->getTotalLoansIssuedForMonth($index+1);
            $interestPaid[] = $this->loanService->getTotalInterestPaidForMonth($index+1);
        }

        $chart->setData([
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Loans Issued (R)',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $loansIssued,
                ],
                [
                    'label' => 'Interest Paid (R)',
                    'backgroundColor' => 'rgb(105, 99, 132)',
                    'borderColor' => 'rgb(105, 99, 132)',
                    'data' => $interestPaid,
                ],
            ],
        ]);

        $chart2->setData([
            'labels' => ['Interest', 'Principal'],
            'datasets' => [[
                'label' => 'Interest vs Principal Paid',
                'data' => [
                    $this->loanService->getTotalInterestCollected(),
                    $this->loanService->getTotalPrincipalCollected()
                ],
                'backgroundColor' => ['rgb(5, 59, 232)','rgb(14, 252, 35)'],
                'hoverOffset' => 8
            ]]
        ]);

        $monthlyStats = [
        'new_loans' => 45,
        'collected_amount' => 15500.50,
        'month' => date('F Y'),
        ];

        return $this->render('reports/monthyReport.html.twig', [
            'controller_name' => 'ReportsController',
            'currentMonth' => date('F'),
            'chart' => $chart,
            'chart2' => $chart2,
            'reportSummary' => $reportSummary,
        ]);
    }
}
