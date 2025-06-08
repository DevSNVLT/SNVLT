<?php

namespace App\Form\Autorisation;

use Doctrine\ORM\QueryBuilder;
use App\Entity\Autorisation\ContratBcbgfh;
use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Repository\References\ExploitantRepository;
use App\Repository\References\ForetRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContratBcbgfhType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero_contrat', TextType::class, [
                'label'=>'N° Contrat',
                'required'=>true,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Le N° du Contrat est obligatoire"
                    ])
                ]
            ])
            ->add('date_contrat', DateType::class, [
                'label'=>'Date Contrat',
                'widget'=>'single_text',
                'required'=>true,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "La date de contrat est obligatoire"
                    ])
                ]

            ])
            ->add('date_signature', DateType::class, [
                'label'=>'Date Signature',
                'widget'=>'single_text',
                'required'=>true,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "La date de contrat est obligatoire"
                    ])
                ]

            ])
            ->add('duree', IntegerType::class, [
                'label'=>'Durée Contrat (mois)',
                'required'=>true,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control',
                    'style'=>'background-color:lightyellow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "La durée du Contrat est obligatoire"
                    ])
                ]
            ])
            ->add('nb_tiges', IntegerType::class, [
                'label'=>'Nombre de tiges',
                'required'=>true,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control',
                    'style'=>'background-color:lightyellow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Le nombre de tiges est obligatoire"
                    ])
                ]
            ])
            ->add('code_foret', EntityType::class, [
                'label'=>'Forêt',
                'class'=>Foret::class,
                'required'=>true,
                'multiple'=>false,
                'expanded'=>false,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control code_foret'
                ],
                'query_builder' => function (ForetRepository $foret): QueryBuilder {
                    return $foret->createQueryBuilder('f')
                        ->andWhere('f.code_type_foret = 2')
                        ->orderBy('f.denomination', 'ASC');
                }

            ])

            ->add('code_exploitant', EntityType::class, [
                'label'=>'Exploitant forestier',
                'class'=>Exploitant::class,
                'required'=>true,
                'multiple'=>false,
                'expanded'=>false,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'query_builder' => function (ExploitantRepository $exploitant): QueryBuilder {
                    return $exploitant->createQueryBuilder('e')
//                        ->andWhere('e.email_personne_ressource is not null')
                        ->orderBy('e.raison_sociale_exploitant', 'ASC');
                },
                'attr'=>[
                    '           class'=>'form-control text-sm text-sm alert-light text-dark code_exploitant'
                ]

            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContratBcbgfh::class,
        ]);
    }
}