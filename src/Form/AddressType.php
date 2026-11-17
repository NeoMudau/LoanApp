<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('streetAddress', TextType::class, [
                'label' => 'Street Address',
                'attr' => ['placeholder' => 'Enter your street address'],
                'constraints' => [new NotBlank()],
            ])
            ->add('suburb', TextType::class, [
                'label' => 'Suburb',
                'attr' => ['placeholder' => 'Enter your suburb'],
                'constraints' => [new NotBlank()],
            ])
            ->add('city', TextType::class, [
                'label' => 'City',
                'attr' => ['placeholder' => 'Enter your city'],
                'constraints' => [new NotBlank()],
            ])
            ->add('province', TextType::class, [
                'label' => 'Province',
                'attr' => ['placeholder' => 'Enter your province'],
                'constraints' => [new NotBlank()],
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Postal Code',
                'attr' => [
                    'placeholder' => 'e.g. 0001',
                    'maxlength' => 10,
                ],
                'constraints' => [new NotBlank()],
            ])
            ->add('country', TextType::class, [
                'label' => 'Country',
                'data' => 'South Africa',
                'constraints' => [new NotBlank()],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
