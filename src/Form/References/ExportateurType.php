<?php

namespace App\Form\References;

use App\Entity\References\Cantonnement;
use App\Entity\References\Exportateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ExportateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sigle', TextType::class, [
                'label'=>'Sigle',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control input-sm font-weight-bold',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;height:25px;'
                ]
            ])

            ->add('raison_sociale_exportateur', TextType::class, [
                'label'=>'Raison sociale',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control input-sm',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;height:25px;'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'La raiso est obligatoire',
                    ])
                ],
                'required'=>true
            ])
            ->add('code_exportateur', TextType::class, [
                'label'=>'Code Exportateur',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control input-sm',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;height:25px;'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le code exportateur est obligatoire',
                    ])
                ],
                'required'=>true
            ])
            ->add('code_cantonnement', EntityType::class, [
                'class'=>Cantonnement::class,
                'label'=>'Cantonnement de rattachement',
                'placeholder'=>'-- Sélectionnez SVP un cantonnement --',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control input-sm',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;height:35px;'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le cantonnement est obligatoire',
                    ])
                ],
                'multiple'=>false,
                'expanded'=>false,
                'required'=>true

            ])
            ->add('adresse_exportateur', TextType::class, [
                'label'=>'Adresse',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control input-sm',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;height:25px;'
                ]

            ])

            ->add('tel_exportateur', TextType::class, [
                'label'=>'Téléphone',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control input-sm',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;height:25px;'
                ]

            ])

            ->add('email_exportateur', TextType::class, [
                'label'=>'Email de la structure',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control input-sm',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;height:25px;'
                ]

            ])

            ->add('date_creation', DateType::class, [
                'label'=>'Date de Création',
                'widget'=>'single_text',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control input-sm',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;height:35px;'
                ]

            ])


            ->add('personne_ressource', TextType::class, [
                'label'=>'Responsable de la structure',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control',
                    'style'=>'background-color:lightblue;text-transform:uppercase;height:25px;'
                ]

            ])

            ->add('email_personne_ressource', TextType::class, [
                'label'=>'Email du responsable',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control',
                    'style'=>'background-color:lightblue;height:25px;'
                ]

            ])

            ->add('mobile_personne_ressource', TextType::class, [
                'label'=>'Téléphone mobile du responsable',
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
            'data_class' => Exportateur::class,
        ]);
    }
}
