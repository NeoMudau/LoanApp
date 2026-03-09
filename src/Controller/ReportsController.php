<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
    public function generateMonthlyReport(): Response
    {
            $monthlyStats = [
            'new_loans' => 45,
            'collected_amount' => 15500.50,
            'month' => date('F Y'),
        ];

        return $this->render('reports/monthyReport.html.twig', [
            'controller_name' => 'ReportsController',
            'stats' => $monthlyStats,
        ]);
    }
}
