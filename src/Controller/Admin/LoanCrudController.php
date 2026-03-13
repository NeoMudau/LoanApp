<?php

namespace App\Controller\Admin;

use App\Entity\Loan;
use App\Entity\Payments;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
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
        if (!$entityInstance instanceof Loan) return;

        $this->calculateLoanFields($entityInstance);
        $entityInstance->calculateClosingDate();
        $entityInstance->updateStatus();
        $entityInstance->setCreatedAt(new \DateTimeImmutable());
        $entityInstance->setUpdatedAt(new \DateTimeImmutable());

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Loan) {
            return;
        }

        $this->calculateLoanFields($entityInstance);
        $entityInstance->calculateClosingDate();
        $entityInstance->updateStatus();

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

            ChoiceField::new('interest_percent', 'Interest %')
                ->setChoices([
                    '30%' => 30,
                    '40%' => 40,
                    '50%' => 50,
                ])
                ->hideOnIndex(),

            ChoiceField::new('loan_term_months', 'Loan Term')
                ->setChoices([
                    '1 Month' => 1,
                    '3 Months' => 3,
                    '6 Months' => 6,
                    '12 Months' => 12,
                    '24 Months' => 24,
                ]),

            NumberField::new('interest_amount')->hideOnForm(),
            NumberField::new('total_payback')->hideOnForm(),
            NumberField::new('balance_remaining')->hideOnForm(),

            DateTimeField::new('created_at')->hideOnForm(),
            DateTimeField::new('closing_at')->hideOnForm(),

            BooleanField::new('extension', 'Allow Extension'),

            ChoiceField::new('status')
            ->setChoices([
                'Active' => 'ACTIVE',
                'Overdue' => 'OVERDUE',
                'Closed' => 'CLOSED'
            ])
            ->renderAsBadges([
                'ACTIVE' => 'success',
                'OVERDUE' => 'danger',
                'CLOSED' => 'secondary'
            ])
            ->hideOnForm()
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('customer_id'));
    }

}
