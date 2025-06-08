<?php

namespace App\Form\References;

use App\Entity\References\Cantonnement;
use App\Entity\References\PosteForestier;
use App\Repository\References\CantonnementRepository;
use App\Repository\References\GrilleLegaliteRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PosteForestierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('denomination', TextType::class, [
                'label'=>'Dénomination du Poste Forestier',
                'label_attr'=>[
                    'class'=>'font-weight-bold text-dark'
                ],
                'attr'=>[
        '           class'=>'form-control alert-light text-dark',
                    'style'=>'text-transform:uppercase; font-size:14px;color:blue;'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le poste forestier est obligatoire',
                    ])

                ]

            ])
            ->add('code_cantonnement', EntityType::class, [
                'label'=>'Sélectionner le cantonnement',
                'label_attr'=>[
                    'class'=>'font-weight-bold text-dark'
                ],
                'class'=> Cantonnement::class,
                'placeholder'=>'-- Sélectionnez le cantonnement --',
                'multiple'=>false,
                'expanded'=>false,
                'attr'=>[
                    '           class'=>'form-control code_dr alert-light text-dark',
                    'style'=>'text-transform:uppercase; font-size:14px;color:blue;'
                ],
                'query_builder' => function (CantonnementRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.nom_cantonnement', 'ASC');
                },
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le cantonnement est obligatoire',
                    ])

                ],

            ])

            ->add('situation_geographique', TextareaType::class, [
                'label'=>'Situation géographique',
                'label_attr'=>[
                    'class'=>'font-weight-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'text-transform:uppercase; font-size:14px;color:blue;height:200px;'
                ]
            ])
            ->add('personneRessource', TextType::class, [
            'label'=>"Personne ressource",
            'label_attr'=>[
                'class'=>'font-weight-bold text-dark',
                'style'=>'text-transform:uppercase;'
            ],

            'attr'=>[
                'class'=>'form-control',
                'style'=>'background-color:lightblue',
                'readonly'=>true
            ]

        ])

        ->add('emailPersonneRessource', TextType::class, [
            'label'=>"Email Personne ressource",
            'label_attr'=>[
                'class'=>'font-weight-bold text-dark'
            ],
            'attr'=>[
                'class'=>'form-control',
                'style'=>'background-color:lightblue',
                'readonly'=>true
            ]

        ])

        ->add('mobilePersonneRessource', TextType::class, [
            'label'=>"Mobile Personne ressource",
            'label_attr'=>[
                'class'=>'font-weight-bold text-dark'
            ],
            'attr'=>[
                'class'=>'form-control',
                'style'=>'background-color:lightblue',
                'readonly'=>true
            ]

        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PosteForestier::class,
            'csrf_protection'=>true
        ]);
    }
}
