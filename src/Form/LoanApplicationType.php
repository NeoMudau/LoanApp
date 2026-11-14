<?php

namespace App\Form;

use App\Entity\Loan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class LoanApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', MoneyType::class, [
                'label' => 'Requested Loan Amount (R)',
                'currency' => 'ZAR',
                'scale' => 2,
                'constraints' => [
                    new NotBlank(),
                    new Positive(),
                ],
            ])

            ->add('loan_term_months', ChoiceType::class, [
                'label' => 'Preferred Repayment Period',
                'choices' => [
                    '1 Month' => 1,
                    '2 Months' => 2,
                    '3 Months' => 3,
                    '6 Months' => 6,
                    '12 Months' => 12,
                ],
                'placeholder' => 'Select repayment period',
                'constraints' => [
                    new NotBlank(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Loan::class,
        ]);
    }
}
