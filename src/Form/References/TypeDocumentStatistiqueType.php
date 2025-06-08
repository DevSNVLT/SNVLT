<?php

namespace App\Form\References;

use App\Entity\References\TypeDocumentStatistique;
use App\Entity\References\Ddef;
use App\Entity\References\Dr;
use App\Entity\References\TypeOperateur;
use App\Repository\References\ForetRepository;
use App\Repository\TypeDocumentStatistiqueRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class TypeDocumentStatistiqueType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('abv', TextType::class, [
                'label'=>'Abréviation',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow'
                ],

                'constraints' => [
                    new NotBlank([
                        'message' => 'Renseignez SVP l\'abréviation du document',
                    ])
                ]
            ])

            ->add('denomination', TextType::class, [
                'label'=>'Dénomination',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Renseignez SVP la dénomination du document',
                    ])
                ]

            ])
            ->add('description', TextareaType::class, [
                'label'=>'Description',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow;height:150px;'
                ],

            ])
            ->add('lettre', CheckboxType::class, [
                'label'=>'Ce document requiert-il une lettre ?',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
				
                    'style'=>'background-color:lightyellow;margin-left:10px;'
                ],

            ])
            ->add('nb_pages', NumberType::class, [
                'label'=>'Nombre de feuillets (pages)',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Renseignez SVP le nobre de feuillets que comporte le document',
                    ])
                ]

            ])
            ->add('tarif', NumberType::class, [
                'label'=>'Tarif (prix de vente en F CFA) ',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Spécifiez SVP le tarif (prix de vente) du document',
                    ])
                ]

            ])
            ->add('stock_alert', NumberType::class, [
                'label'=>$this->translator->trans('Stock alert'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Renseignez SVP le nobre de feuillets que comporte le document',
                    ])
                ]

            ])
            ->add('statut', ChoiceType::class, [
                'label'=>'Satut',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'choices'=>[
                    'ACTIF'=>'ACTIF',
                    'INACTIF'=>'INACTIF'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Renseignez SVP le statut du document',
                    ])
                ]

            ])

            ->add('code_type_operateur', EntityType::class, [
                'label'=>'Relatif à ',
                'class'=>TypeOperateur::class,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'placeholder'=>'Sélectionnez le type Opérateur',
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightblue',
                    'placeholder'=>'Sélectionnez le type Opérateur'
                ],
                'required'=>true,

                'constraints' => [
                    new NotBlank([
                        'message' => 'Renseignez SVP le Type Opérateur',
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
            'data_class' => TypeDocumentStatistique::class,
        ]);
    }
}
