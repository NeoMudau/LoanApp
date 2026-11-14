<?php

namespace App\Form;

use App\Entity\EmploymentDetails;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class EmploymentDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Employer Name',
                'attr' => ['placeholder' => 'Enter your employer name'],
                'constraints' => [new NotBlank()],
            ])
            ->add('position', TextType::class, [
                'label' => 'Job Position',
                'attr' => ['placeholder' => 'e.g. Software Developer'],
                'constraints' => [new NotBlank()],
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Employment Duration (Months)',
                'attr' => [
                    'placeholder' => 'e.g. 18',
                    'min' => 1,
                ],
                'constraints' => [
                    new NotBlank(),
                    new Positive(),
                ],
            ])
            ->add('salary', NumberType::class, [
                'label' => 'Salary',
                'scale' => 2,
                'attr' => [
                    'placeholder' => 'Enter your salary',
                    'min' => 0.01,
                    'step' => '0.01',
                ],
                'constraints' => [
                    new NotBlank(),
                    new Positive(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EmploymentDetails::class,
        ]);
    }
}
