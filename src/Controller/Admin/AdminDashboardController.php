<?php

namespace App\Controller\Admin;

use App\Entity\ArchivedCustomers;
use App\Entity\Customer;
use App\Entity\HistoryCustomers;
use App\Entity\Loan;
use App\Entity\Payments;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[AdminDashboard(routePath: '/admin/dashboard', routeName: 'admin')]
class AdminDashboardController extends AbstractDashboardController
{
    private ChartBuilderInterface $chartBuilder;

    public function __construct(
        ChartBuilderInterface $chartBuilder,
        
    )
    {
        $this->chartBuilder = $chartBuilder;
    }

    public function index(): Response
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

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

        $monthlyStats = [
        'new_loans' => 45,
        'collected_amount' => 157500.50,
        'month' => date('F Y'),
        'totalLoans' => 157,
        'totalCustomers' => 84,
        'overdueLoans' => 5,
        'monthlyInterest' => 14500,
        ];

        return $this->render('admin_dashboard/index.html.twig', [
        'stats' => $monthlyStats,
        'chart' => $chart,
    ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Loan App');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Customers', 'fa fa-user', Customer::class);
        yield MenuItem::linkToCrud('Loans', 'fa fa-money-bill', Loan::class);
        yield MenuItem::linkToCrud('Payments', 'fa fa-credit-card', Payments::class);

        yield MenuItem::linkToCrud('History Customers', 'fa fa-history', HistoryCustomers::class)
            ->setPermission('ROLE_SUPER_ADMIN'); 
        yield MenuItem::linkToCrud('Archived Customers', 'fa fa-archive', ArchivedCustomers::class)
            ->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linkToCrud('Users', 'fa fa-user-shield', User::class)
            ->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linkToRoute('Overdue Loans', 'fa fa-exclamation-triangle', 'overdue_loans');
        yield MenuItem::linkToRoute('Monthly Report', 'fa fa-chart-line', 'monthly_report');
        yield MenuItem::linkToRoute('Settings', 'fa fa-cogs', 'loan_settings')
            ->setPermission('ROLE_SUPER_ADMIN');
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addAssetMapperEntry('app');
    }
}
