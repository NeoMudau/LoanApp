<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('full_name', TextType::class, [
                    'constraints' => [
                        new NotBlank(),
                        new Length([
                            'min' => 2,
                            'max' => 50,
                        ]),
                        new Regex([
                            'pattern' => '/^[a-zA-Z\s]+$/',
                            'message' => 'Only letters and spaces are allowed.',
                        ]),
                    ],
                ])
            ->add('surname', TextType::class, [
                    'constraints' => [
                        new NotBlank(),
                        new Length([
                            'min' => 2,
                            'max' => 50,
                        ]),
                        new Regex([
                            'pattern' => '/^[a-zA-Z\s]+$/',
                        ]),
                    ],
                ])
            ->add('date_of_birth', BirthdayType::class, [
                    'label' => 'Date of birth',
                    'constraints' => [
                        new NotBlank(),
                        new LessThan([
                            'value' => 'today',
                            'message' => 'Date of birth must be in the past.',
                        ]),
                    ],
                ])
            ->add('gender', ChoiceType::class, [
                    'choices' => [
                        'Male' => 'Male',
                        'Female' => 'Female',
                        'Other' => 'Other',
                    ],
                    'constraints' => [
                        new NotBlank(),
                    ],
                ])
            ->add('phone', TelType::class, [
                    'constraints' => [
                        new NotBlank(),
                        new Regex([
                            'pattern' => '/^\+?[0-9]{10,15}$/',
                            'message' => 'Enter a valid phone number.',
                        ]),
                    ],
                ])
            ->add('street_address', TextType::class, [
                    'constraints' => [
                        new NotBlank(),
                        new Length([
                            'max' => 100,
                        ]),
                    ],
                ])
            ->add('suburb', TextType::class, ['constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 2,
                        'max' => 50,
                    ]),
                ]])
            ->add('city', TextType::class, [
                    'constraints' => [
                        new NotBlank(),
                        new Length([
                            'min' => 2,
                            'max' => 50,
                        ]),
                    ]
                ])
            ->add('postal_code', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern' => '/^[0-9]{4}$/',
                        'message' => 'Please enter a valid South African postal code.',
                    ]),],
                ])
            ->add('country', CountryType::class, ['placeholder' => 'Select your country', 
                'constraints' => [
                    new NotBlank(),
                ]])
            ->add('province', TextType::class, ['placeholder' => 'Enter your province',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 2,
                        'max' => 50,
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z\s]+$/',
                    ])]
                ])
            ->add('employer', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => 100,
                    ]),
                ],
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Duration in Years',
                'constraints' => [
                    new NotBlank(),
                    new PositiveOrZero(),
                ],
            ])
            ->add('position', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 2,
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('payslip', FileType::class, [
                'label' => 'Upload Payslip (PDF only)',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'application/pdf',
                            'extensions' => ['pdf'],
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF file.',
                    ]),
                ],
            ])
            //
            ->add('submit', SubmitType::class, ['label' => 'Submit'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
