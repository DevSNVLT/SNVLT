<?php

namespace App\Form\Administration;

use App\Entity\Administration\DemandeOperateur;
use App\Entity\References\TypeDocumentStatistique;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ValidationDemandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('doc_stat', EntityType::class, [
                'label'=>'Document',
                'class'=>TypeDocumentStatistique::class ,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark',
                    'readonly'=>true
                ],
                'attr'=>[
        '           class'=>'form-control alert-light text-dark fs-2 codetype'
                ]
            ])

            ->add('qte', NumberType::class, [
                'label'=>'Quantité demandée',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark fs-2'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark fs-2 text-center text-danger'
                ]
            ])
            ->add('qte_validee', NumberType::class, [
                            'label'=>'Quantité à valider',
                            'label_attr'=>[
                                'class'=>'fw-bold text-dark fs-2'
                            ],
                            'attr'=>[
                                '           class'=>'form-control alert-light text-dark fs-2 text-center text-danger'
                            ],

                            'constraints' => [
                                new NotBlank([
                                    'message' => 'La quantité est obligatoire',
                                ]),
                                new Length([
                                    'max'=>200,
                                    'maxMessage'=>'Le maximum de documents à générer est {{ limit }}',
                                    'min'=>1,
                                    'minMessage'=>'Le minimum de documents à générer est {{ limit }}',
                                ]),
                            ]

                        ])

            ->add('motif_verification', TextareaType::class, [
                'label'=>'Motif',
                'attr'=>[
                    'class'=>'form-control alert-light text-dark'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DemandeOperateur::class,
        ]);
    }
}
