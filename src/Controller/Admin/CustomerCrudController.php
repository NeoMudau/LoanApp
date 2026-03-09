<?php

namespace App\Controller\Admin;

use App\Entity\Customer;
use App\Entity\Loan;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class CustomerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Customer::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'First Name'),
            TextField::new('surname', 'Last Name'),
            TelephoneField::new('phone', 'Phone Number'),
            NumberField::new('salary', 'Monthly Salary'),

            DateTimeField::new('created_at')
                ->hideOnForm()   // auto-set, not editable
                ->setSortable(true),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewLoans = Action::new('viewLoans', 'View Loans')
            ->linkToUrl(function (Customer $customer) {
                $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
                return $adminUrlGenerator
                    ->setController(LoanCrudController::class)
                    ->set('filters[customer_id][comparison]', '=')
                    ->set('filters[customer_id][value]', $customer->getId())
                    ->generateUrl();
            });

        return $actions
            ->add(Crud::PAGE_INDEX, $viewLoans)
            ->add(Crud::PAGE_DETAIL, $viewLoans);
    }

    /**
     * Custom action: Show only loans for this customer
     */
    public function listLoans(AdminUrlGenerator $adminUrlGenerator)
    {
        $request = $this->getContext()->getRequest();
        $id = $request->query->get('entityId'); // safe and works from index + detail pages

        if (!$id) {
            throw new \Exception('No customer selected.');
        }

        $url = $adminUrlGenerator
            ->setController(LoanCrudController::class)
            ->set('filters[customer_id][comparison]', '=')
            ->set('filters[customer_id][value]', $id)
            ->generateUrl();

        return $this->redirect($url);
    }

}
