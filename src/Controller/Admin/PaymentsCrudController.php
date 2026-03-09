<?php

namespace App\Controller\Admin;

use App\Entity\Payments;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class PaymentsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Payments::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('loan_id', 'Loan'),
            NumberField::new('amount_paid', 'Amount Paid'),
            DateTimeField::new('payment_date')->hideOnForm(),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Payments) return;

        // 1. Auto-set payment date
        $entityInstance->setPaymentDate(new \DateTimeImmutable());

        // 2. Get the loan
        $loan = $entityInstance->getLoanId();
        if ($loan) {
            $interestAmount = $loan->getInterestAmount();   // e.g., 600
            $totalPayback = $loan->getTotalPayback();       // e.g., 2600
            $balanceRemaining = $loan->getBalanceRemaining() ?? $totalPayback;

            $paidAmount = $entityInstance->getAmountPaid();

            if ($paidAmount == $interestAmount) {
                // If only interest is paid, balance remains the same
                $newBalance = $balanceRemaining;
            } elseif ($paidAmount != $interestAmount) {
                // Paid more than interest
                $principalPaid = $paidAmount; // or subtract interest? Depending on your logic
                $newPrincipal = $balanceRemaining - $principalPaid;

                // Recalculate new balance including interest percentage
                $interestPercent = $loan->getInterestPercent() / 100; // convert 30% to 0.3
                $newBalance = $newPrincipal + ($newPrincipal * $interestPercent);
            }
            // else {
            //     // Paid less than interest? Just subtract from balance normally
            //     $newBalance = $balanceRemaining - $paidAmount;
            // }

            $loan->setBalanceRemaining($newBalance);
            $entityManager->persist($loan);
        }

        // 3. Persist the payment itself
        parent::persistEntity($entityManager, $entityInstance);
    }
}
