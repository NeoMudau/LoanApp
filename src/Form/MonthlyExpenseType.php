<?php

namespace App\Form;

use App\Entity\MonthlyExpense;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class MonthlyExpenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', ChoiceType::class, [
                'label' => 'Expense Category',
                'placeholder' => 'Select expense category',
                'choices' => [
                    'Accommodation / Rent' => 'Accommodation / Rent',
                    'Food and Groceries' => 'Food and Groceries',
                    'Transport' => 'Transport',
                    'Electricity and Water' => 'Electricity and Water',
                    'Debt Repayments' => 'Debt Repayments',
                    'Insurance' => 'Insurance',
                    'Medical Expenses' => 'Medical Expenses',
                    'Education' => 'Education',
                    'Family Responsibilities' => 'Family Responsibilities',
                    'Other' => 'Other',
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('amount', MoneyType::class, [
                'label' => 'Monthly Amount (R)',
                'currency' => 'ZAR',
                'scale' => 2,
                'constraints' => [
                    new NotBlank(),
                    new Positive(),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description (Optional)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Add additional details...',
                    'rows' => 3,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MonthlyExpense::class,
        ]);
    }
}
