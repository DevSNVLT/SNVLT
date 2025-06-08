<?php

namespace App\Form\References;

use App\Entity\References\AttributairePs;
use App\Entity\References\Ddef;
use App\Entity\References\Dr;
use App\Entity\References\Nationalite;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class AttributairePsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('raison_sociale', TextType::class, [
                'label'=>"Raison sociale",
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control input-sm',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;height:25px;'
                ],
                'required'=>true,
                'constraints' => [
                    new NotBlank([
                        'message' => "La raison sociale de l'Attributaire est obligatoire",
                    ])
                ]
            ])
            ->add('code', TextType::class, [
                            'label'=>"Code",
                            'label_attr'=>[
                                'class'=>'fw-bold text-dark'
                            ],
                            'attr'=>[
                                'class'=>'form-control',
                                'style'=>'background-color:lightyellow;text-transform:uppercase;height:25px;'
                            ],
                            'required'=>true,
                            'constraints' => [
                                new NotBlank([
                                    'message' => "Le code est obligatoire",
                                ])
                            ]
                        ])

            ->add('type_atributaire', ChoiceType::class, [
                'label'=>'Type Attributaire',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'choices' =>[
                    'PERSONNE PHYSIQUE'=>'PERSONNE PHYSIQUE',
                    'PERSONNE MORALE'=>'PERSONNE MORALE',
                    'EXPLOITANT FORESTIER'=>'EXPLOITANT FORESTIER'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;height:35px;'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "SVP Renseignez Le type",
                    ])
                ]

            ])
            ->add('adresse', TextType::class, [
                'label'=>"Adresse",
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;height:25px;'
                ]
            ])
            ->add('contact', TextType::class, [
                'label'=>"Contact",
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;height:25px;'
                ]
            ])
            ->add('nationalite', EntityType::class, [
                'label'=>'Nationalité',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'placeholder'=>"Sélectionnez la nationalité",
                'class'=> Nationalite::class,
                'multiple'=>false,
                'expanded'=>false,
                'attr'=>[
                    'class'=>'form-control code_ddef alert-light text-dark',
                    'placeholder'=>"Sélectionnez la nationalité",
                    'style'=>'background-color:lightyellow;text-transform:uppercase;height:35px;'
                ]

            ])
            ->add('sexe', ChoiceType::class, [
                'label'=>'Genre',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'choices' =>[
                    'M'=>'M',
                    'F'=>'F'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;height:35px;'
                ]

            ])
            ->add('cc', TextType::class, [
                'label'=>"Compte Contribuable",
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;height:25px;'
                ]
            ])
            ->add('statut', CheckboxType::class, [
                'label'=>'Actif ?',
                'label_attr'=>[
                    'class'=>'font-weight-bold text-danger'
                ],'attr'=>[
                    'class'=>'form-check-input mt-1 mb-2',
                    'style'=>'cursor: pointer;margin-left:10px;',
                    'role'=>'switch'
                ]
            ])

            ->add('personne_ressource', TextType::class, [
                'label'=>"Personne ressource",
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],

                'attr'=>[
                    'class'=>'form-control',
                    'style'=>'background-color:lightblue;text-transform:uppercase;height:25px;'
                ]

            ])

            ->add('email_personne_ressource', TextType::class, [
                'label'=>"Email Personne ressource",
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control',
                    'style'=>'background-color:lightblue;height:25px;'
                ]

            ])


            ->add('mobile_personne_ressource', TextType::class, [
                'label'=>"Mobile Personne ressource",
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control',
                    'style'=>'background-color:lightblue;height:25px;'
                ]

            ])



        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AttributairePs::class,
        ]);
    }
}
