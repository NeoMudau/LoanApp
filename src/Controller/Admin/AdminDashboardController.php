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
use App\Repository\LoanApplicationRepository;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

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

        $months = ['Jan','Feb','Mar','Apr','May'];
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
            'labels' => ['Collected', 'Issued'],
            'datasets' => [[
                'label' => 'Interest vs Principal Paid',
                'data' => [
                    $this->loanService->getTotalPrincipalCollected(),
                    $this->loanService->getTotalPrincipalIssued()
                ],
                'backgroundColor' => ['rgb(25, 129, 132)','rgb(14, 252, 35)'],
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
            ->setTitle('Quick Loan');
    }

    #[Route('/admin/loan-applications', name: 'admin_loan_applications')]
    public function loanApplications(
        LoanApplicationRepository $repository
    ): Response {
        $applications = $repository->findBy(
            [],
            ['createdAt' => 'DESC']
        );

        return $this->render('admin_dashboard/applications/index.html.twig', [
            'applications' => $applications,
        ]);
    }

    #[Route(
        '/admin/loan-applications/{id}/review',
        name: 'admin_loan_application_review',
        requirements: ['id' => '\d+']
    )]
    public function reviewLoanApplication(
        int $id,
        LoanApplicationRepository $repository
    ): Response {
        $application = $repository->find($id);

        if (!$application) {
            throw $this->createNotFoundException(
                'Loan application not found.'
            );
        }

        $customer = $application->getCustomer();
        $employment = $customer->getEmploymentDetails();

        $monthlyIncome = $employment?->getSalary() ?? 0;

        $expenses = $customer->getMonthlyExpenses();

        $totalExpenses = 0;

        foreach ($expenses as $expense) {
            $totalExpenses += $expense->getAmount();
        }

        $disposableIncome = $monthlyIncome - $totalExpenses;

        return $this->render('admin_dashboard/applications/review.html.twig', [
            'application' => $application,
            'employment' => $employment,
            'monthlyIncome' => $monthlyIncome,
            'expenses' => $expenses,
            'totalExpenses' => $totalExpenses,
            'disposableIncome' => $disposableIncome,
            'scenario30' => $disposableIncome * 0.30,
            'scenario40' => $disposableIncome * 0.40,
            'scenario50' => $disposableIncome * 0.50,
        ]);
    }

    #[Route(
        '/admin/loan-applications/{id}/start-review',
        name: 'admin_loan_application_start_review',
        requirements: ['id' => '\d+'],
        methods: ['POST']
    )]
    public function startReview(
        Request $request,
        int $id,
        LoanApplicationRepository $repository,
        EntityManagerInterface $entityManager
    ): Response {
        $application = $repository->find($id);

        if (!$application) {
            throw $this->createNotFoundException(
                'Loan application not found.'
            );
        }

        if (!$this->isCsrfTokenValid(
            'start_review_' . $id,
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        if ($application->getStatus() !== 'SUBMITTED') {
            $this->addFlash('warning', 'This application cannot be moved to review.');

            return $this->redirectToRoute(
                'admin_loan_application_review',
                ['id' => $id]
            );
        }

        $application->setStatus('UNDER_REVIEW');

        $entityManager->flush();

        $this->addFlash('success', 'Application successfully moved to Under Review.');

        return $this->redirectToRoute(
            'admin_loan_application_review',
            ['id' => $id]
        );
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Customers', 'fa fa-user', Customer::class);
        yield MenuItem::linkToCrud('Loans', 'fa fa-money-bill', Loan::class);
        yield MenuItem::linkToRoute('Loan Applications', 'fa fa-file-invoice-dollar', 'admin_loan_applications');
        yield MenuItem::linkToCrud('Payments', 'fa fa-credit-card', Payments::class);

        yield MenuItem::linkToCrud('History Customers', 'fa fa-history', HistoryCustomers::class)
            ->setPermission('ROLE_SUPER_ADMIN'); 
        yield MenuItem::linkToCrud('Archived Customers', 'fa fa-archive', ArchivedCustomers::class)
            ->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linkToCrud('Users', 'fa fa-user-shield', User::class)
            ->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linkToRoute('Overdue Loans', 'fa fa-exclamation-triangle', 'overdue_loans');
        yield MenuItem::linkToRoute('Monthly Report', 'fa fa-chart-line', 'monthly_report');
        yield MenuItem::linkToRoute('Settings', 'fa fa-cogs', 'app_settings')
            ->setPermission('ROLE_SUPER_ADMIN');
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addAssetMapperEntry('app');
    }
}
