<?php

namespace App\Form\Autres;

use App\Entity\Autres\Contacter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContacterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control'
                ],

                'constraints'=>[
                    new NotBlank([
                        'message'=>'Saisissez votre Nom et Prénom(s) SVP'
                    ])
                ]
            ])
            ->add('email', TextType::class, [
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control'
                ],

                'constraints'=>[
                    new NotBlank([
                        'message'=>'Saisissez votre Nom et Prénom(s) SVP'
                    ])
                ]
            ])
            ->add('contact', TextType::class, [
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control'
                ],

                'constraints'=>[
                    new NotBlank([
                        'message'=>'Saisissez votre Nom et Prénom(s) SVP'
                    ])
                ]
            ])
            ->add('subject', TextType::class, [
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control'
                ],

                'constraints'=>[
                    new NotBlank([
                        'message'=>'Saisissez votre Nom et Prénom(s) SVP'
                    ])
                ]
            ])
            ->add('message', TextType::class, [
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control'
                ],

                'constraints'=>[
                    new NotBlank([
                        'message'=>'Saisissez votre Nom et Prénom(s) SVP'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contacter::class,
        ]);
    }
}
