<?php

namespace App\Form\References;

use App\Entity\References\NaturePs;
use App\Entity\References\TypeDocumentStatistique;
use App\Entity\References\TypeDossierPs;
use App\Entity\References\TypeOperateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class NaturePsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, [
                'label'=>'Libellé',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow;text-transform:uppercase'
                ],

                'constraints' => [
                    new NotBlank([
                        'message' => 'Renseignez SVP l\'abréviation du document',
                    ])
                ]
            ])

            ->add('unite', ChoiceType::class, [
                'label'=>'Dénomination',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'choices' =>[
                    'TONNE'=>'TONNE',
                    'SAC'=>'SAC',
                    'LITRE'=>'LITRE'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "SVP Renseignez l'unité",
                    ])
                ]

            ])
            ->add('montantAutorisation', IntegerType::class, [
                'label'=>'Montant Autorisation',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "SVP Renseignez le montant",
                    ])
                ]

            ])
            ->add('dureeAutorisation', IntegerType::class, [
                'label'=>'Durée',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Renseignez SVP la durée',
                    ])
                ]

            ])

            ->add('typeDossierPs', EntityType::class, [
                'label'=>'Type Dossier ',
                'class'=>TypeDossierPs::class,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'placeholder'=>'Sélectionnez le type Dossier',
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightblue',
                    'placeholder'=>'Sélectionnez le type Opérateur'
                ],
                'required'=>true,

                'constraints' => [
                    new NotBlank([
                        'message' => 'Renseignez SVP le Type Dossier',
                    ])
                ],
                'multiple'=>false,
                'expanded'=>false

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NaturePs::class,
        ]);
    }
}