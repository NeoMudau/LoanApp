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
        if ($loan === null) {
            throw new \LogicException('Payment must have a Loan assigned.');
        }

        // Apply payment
        $refund = $loan->applyPayment($entityInstance->getAmountPaid());

        // Add payment to the loan collection
        $loan->addPayment($entityInstance);

        $entityManager->persist($loan);
        parent::persistEntity($entityManager, $entityInstance);
    }
}
