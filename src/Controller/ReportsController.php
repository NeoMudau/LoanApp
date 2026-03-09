<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

final class ReportsController extends AbstractController
{
    #[Route('/reports', name: 'app_reports')]
    public function index(): Response
    {
        return $this->render('reports/index.html.twig', [
            'controller_name' => 'ReportsController',
        ]);
    }

    #[Route('/monthly/report', name: 'monthly_report')]
    public function generateMonthlyReport(ChartBuilderInterface $chartBuilder): Response
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart2 = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);

        $chart->setData([
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'datasets' => [
                [
                    'label' => 'Loans Issued (R)',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => [0, 2500, 1800, 4000, 3500, 5000],
                ],

                [
                    'label' => 'Interest Paid (R)',
                    'backgroundColor' => 'rgb(105, 99, 132)',
                    'borderColor' => 'rgb(105, 99, 132)',
                    'data' => [0, 750, 540, 1200, 1050, 1500],
                ],
            ],
        ]);

        $chart2->setData([
            'labels' => ['Female', 'Male'],
            'datasets' => [
                [
                'label' => 'Gender',
                'data' => [12, 33],
                'backgroundColor' => [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                ],
                'hoverOffset' => 8
                ],
            ],
        ]);

        $monthlyStats = [
        'new_loans' => 45,
        'collected_amount' => 15500.50,
        'month' => date('F Y'),
        ];

        return $this->render('reports/monthyReport.html.twig', [
            'controller_name' => 'ReportsController',
            'chart' => $chart,
            'chart2' => $chart2,
            'stats' => $monthlyStats,
        ]);
    }
}
