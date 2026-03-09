<?php

namespace App\Controller\Admin;

use App\Entity\Loan;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumberFilter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class LoanCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Loan::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Loan) {
            return;
        }

        $this->calculateLoanFields($entityInstance);

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Loan) {
            return;
        }

        $this->calculateLoanFields($entityInstance);

        parent::updateEntity($entityManager, $entityInstance);
    }

    private function calculateLoanFields(Loan $loan): void
    {
        // Creation date (only if new)
        if ($loan->getCreatedAt() === null) {
            $loan->setCreatedAt(new \DateTimeImmutable());
        }

        // Interest amount
        $interestAmount = ($loan->getAmount() * $loan->getInterestPercent()) / 100;
        $loan->setInterestAmount($interestAmount);

        // Total payback
        $totalPayback = $loan->getAmount() + $interestAmount;
        $loan->setTotalPayback($totalPayback);

        // Remaining balance = total (until payments reduce it)
        if ($loan->getBalanceRemaining() === null) {
            $loan->setBalanceRemaining($totalPayback);
        }
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('customer_id', 'Customer'),

            NumberField::new('amount'),
            
            // Interest % dropdown
            ChoiceField::new('interest_percent', 'Interest %')
            ->setChoices([
                '30%' => 30,
                '40%' => 40,
                '50%' => 50,
            ])
            ->hideOnIndex(),

            // Hide calculated fields on form
            NumberField::new('interest_amount')->hideOnForm(),
            NumberField::new('total_payback')->hideOnForm(),
            NumberField::new('balance_remaining')->hideOnForm(),
            DateTimeField::new('created_at')->hideOnForm(),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('customer_id'));

    }

}
