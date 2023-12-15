<?php

namespace App\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CalculationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'product',
                TextType::class,
                [
                    'constraints' => [
                        new NotNull(),
                        new Type('numeric')
                    ]
                ]
            )
            ->add(
                'count',
                TextType::class,
                [
                    'constraints' => [
                        new NotNull(),
                        new Type('numeric'),
                        new GreaterThan(0)
                    ]
                ]
            )
            ->add(
                'taxNumber',
                TextType::class,
                [
                    'constraints' => [
                        new NotNull(),
                        new Callback([$this, 'validateTaxNumber'])
                    ]
                ]
            )
            ->add(
                'couponCode',
                TextType::class,
            );
    }

    /**
     * @param $value
     * @param ExecutionContextInterface $context
     * @return void
     */
    public function validateTaxNumber($value, ExecutionContextInterface $context): void
    {
        if (
            !preg_match('/^DE[0-9]{9}$/', $value)
                &&
            !preg_match('/^IT[0-9]{11}$/', $value)
                &&
            !preg_match('/^GR[0-9]{9}$/', $value)
        ) {
            $context->addViolation('invalid tax number format');
        }
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}
