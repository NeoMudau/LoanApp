<?php

namespace App\Controller\Admin;

use App\Entity\ArchivedCustomers;
use App\Entity\Customer;
use App\Entity\HistoryCustomers;
use App\Entity\Loan;
use App\Entity\Payments;
use App\Entity\User;
use App\Service\LoanService;
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
    private LoanService $loanService;

    public function __construct(
        ChartBuilderInterface $chartBuilder,
        LoanService  $loanService,
    )
    {
        $this->chartBuilder = $chartBuilder;
        $this->loanService = $loanService;
    }

    public function index(): Response
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $chart2 = $this->chartBuilder->createChart(Chart::TYPE_DOUGHNUT);

        $adminKPI = $this->loanService->getAdminKpiSummary();
        $adminReport = $this->loanService->getReportsSummary();

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

        return $this->render('admin_dashboard/index.html.twig', [
            'adminKPI' => $adminKPI,
            'adminReport' => $adminReport,
            'currentMonth' => date('F'),
            'chart' => $chart,
            'chart2' => $chart2,
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
