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

#[AdminDashboard(routePath: '/admin/dashboard', routeName: 'admin')]
class AdminDashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('admin_dashboard/index.html.twig', [
        'totalLoans' => 150000,
        'totalCustomers' => 120,
        'overdueLoans' => 5,
        'monthlyInterest' => 4500,
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
}
