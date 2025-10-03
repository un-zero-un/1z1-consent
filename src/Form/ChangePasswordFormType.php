<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'options' => ['attr' => ['autocomplete' => 'new-password']],
                    'first_options' => [
                        'constraints' => [
                            new NotBlank(['message' => 'Please enter a password']),
                            new Length([
                                'min' => 6,
                                'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                                'max' => 4096,
                            ]),
                        ],
                        'label' => 'Nouveau mot de passe',
                    ],
                    'second_options' => ['label' => 'Répéter le mot de passe'],
                    'invalid_message' => 'Les mot de passe ne correspondent pas',
                    'mapped' => false,
                ],
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
