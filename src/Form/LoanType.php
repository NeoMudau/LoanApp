<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Loan;
use App\Entity\Payments;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount')
            ->add('interest_percent')
            ->add('interest_amount')
            ->add('total_payback')
            ->add('balance_remaining')
            ->add('created_at', null, [
                'widget' => 'single_text',
            ])
            ->add('customer_id', EntityType::class, [
                'class' => Customer::class,
                'choice_label' => 'id',
            ])
            ->add('payments', EntityType::class, [
                'class' => Payments::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Loan::class,
        ]);
    }
}
