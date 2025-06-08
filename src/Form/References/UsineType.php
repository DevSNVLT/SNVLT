<?php

namespace App\Form\References;

use App\Entity\References\Cantonnement;
use App\Entity\References\Exploitant;
use App\Entity\References\TypeTransformation;
use App\Entity\References\Usine;
use App\Repository\References\ExploitantRepository;
use App\Repository\References\CantonnementRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UsineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero_usine', NumberType::class, [
                'label'=>'Renseignez le N° de l\'usine',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le numéro usine est obligatoire',
                    ])
                ],
                'required'=>true
            ])

            ->add('raison_sociale_usine', TextType::class, [
                'label'=>'Raison sociale de l\'usine',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseignez la raison sociale° de l\'usine',
                    ])
                ],
                'required'=>true
            ])

            ->add('sigle', TextType::class, [
                'label'=>'Sigle',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;'
                ]
            ])
            ->add('agree', CheckboxType::class, [
                'label'=>'Cette usine a t-elle son agréement ?',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark alert-info'
                ],
                'attr'=>[
                    'class'=>'form-check-input mt-1 mb-2',
                    'style'=>'cursor: pointer;',
                    'role'=>'switch'
                ]
            ])
            ->add('personne_ressource', TextType::class, [
                'label'=>'Personne ressource',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightblue;text-transform:uppercase;',
                    'readonly'=>true
                ]
            ])

            ->add('cc_usine', TextType::class, [
                'label'=>'Compte contribuable',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;'
                ]
            ])

            ->add('tel_usine', TextType::class, [
                'label'=>'Numéro Téléphone',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;'
                ]
            ])
            ->add('fax_usine', TextType::class, [
                'label'=>'Fax',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;'
                ]
            ])
            ->add('adresse_usine', TextType::class, [
                'label'=>'Adresse',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;'
                ]
            ])

            ->add('localisation_usine', TextType::class, [
                'label'=>'Localisation',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;'
                ]
            ])
            ->add('ville', TextType::class, [
                'label'=>'Ville',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;'
                ]
            ])
            ->add('capacite_usine', NumberType::class, [
                'label'=>'Capacité',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;'
                ]
            ])


            ->add('email_personne_ressource', TextType::class, [
                'label'=>'Email Personne ressource',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightblue;text-transform:uppercase;',
                    'readonly'=>true
                ]
            ])
            ->add('mobile_personne_ressource', TextType::class, [
                'label'=>'Mobile Perssone ressource',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightblue;text-transform:uppercase;',
                    'readonly'=>true
                ]
            ])

            ->add('export', CheckboxType::class, [
                'label'=>'Cette usine est-elle exportatrice de bois ?',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark alert-info'
                ],
                'attr'=>[
                    'class'=>'form-check-input mt-1 mb-2',
                    'style'=>'cursor: pointer;',
                    'role'=>'switch'
                ]
            ])

            ->add('code_exportateur', TextType::class, [
                'label'=>'Code Exportateur',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow'
                ]
            ])

            ->add('type_transformation', EntityType::class, [
                'label'=>'Type de transformation',
                'class'=> TypeTransformation::class,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'choices form-control form-select multiple-remove typetransfo',
                    'style'=>'background-color:lightyellow'
                ],
                'multiple'=>true,
                'expanded'=>false
            ])

            ->add('code_cantonnement', EntityType::class, [
                'label'=>'Sélectionner le cantonnement',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'placeholder'=>'Sélectionner le cantonnement',
                'class'=> Cantonnement::class,

                'multiple'=>false,
                'expanded'=>false,
                'attr'=>[
                    ' class'=>'form-control code_cantonnement alert-light text-dark',
                    'placeholder'=>'--Sélectionnez le cantonnement forestier...',
                    'style'=>'background-color:lightyellow'
                ],
                'query_builder' => function (CantonnementRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.nom_cantonnement', 'ASC');
                }
            ])

            ->add('code_exploitant', EntityType::class, [
                'label'=>'Sélectionnez le compte Exploitant en cas d\'activités forestière',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark',
                    'style'=>'background:lightred; font-weight:bold;'
                ],
                'placeholder'=>'Sélectionner le compte exploitant',
                'class'=> Exploitant::class,
                'multiple'=>false,
                'expanded'=>false,
                'attr'=>[
                    ' class'=>'form-control code_exploitant alert-danger text-dark',
                    'placeholder'=>'--Sélectionnez le compte exploitant...',
                    'style'=>'font-size:20px;'
                ],
                'query_builder' => function (ExploitantRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.raison_sociale_exploitant', 'ASC');
                }
            ])
            ->add('latitude', NumberType::class, [
                'label'=>false,
                'label_attr'=>[
                    'class'=>'font-weight-bold text-dark'
                ],
                'attr'=>[
                    'placeholder'=>'Latitude',
                    'class'=>'form-control alert-light text-primary latitude',
                    'readOnly'=>true
                ]
            ])
            ->add('longitude', NumberType::class, [
                'label'=>false,
                'label_attr'=>[
                    'class'=>'font-weight-bold text-dark'
                ],
                'attr'=>[
                    'placeholder'=>'Longitude',
                    'class'=>'form-control alert-light text-primary longitude',
                    'readOnly'=>true
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usine::class,
        ]);
    }
}
