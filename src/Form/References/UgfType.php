<?php

namespace App\Form\References;

use App\Entity\References\Ugf;
use App\Entity\References\Ddef;
use App\Entity\References\Dcg;
use App\Entity\References\Foret;
use App\Repository\References\ForetRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\QueryBuilder;

class UgfType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('denomination', TextType::class, [
                'label'=>'Dénomination UGF',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control',
                    'style'=>'background-color:lightyellow'
                ],
                'required'=>true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'La dénomination est obligatoire',
                    ])
                ]
            ])

            ->add('personneRessource', TextType::class, [
                'label'=>'Responsable UGF',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],

                'attr'=>[
                    'class'=>'form-control',
                    'style'=>'background-color:lightblue',
                    'readonly'=>true
                ]

            ])

            ->add('emailPersonneRessource', TextType::class, [
                'label'=>'Email Responsable',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control',
                    'style'=>'background-color:lightblue',
                    'readonly'=>true
                ]

            ])


            ->add('mobilePersonneRessource', TextType::class, [
                'label'=>'Mobile responsable',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control',
                    'style'=>'background-color:lightblue',
                    'readonly'=>true
                ]

            ])

            ->add('code_dcg', EntityType::class, [
                'label'=>'Sélectionnez la DCG',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'class'=> Dcg::class,
                'multiple'=>false,
                'expanded'=>false,
                'placeholder'=>'Sélectionnez la DCG',
                'attr'=>[
                    ' class'=>'form-control code_dr',
                    'placeholder'=>'Sélectionnez la DCG'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'La DCG est obligatoire',
                    ])
                ],

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ugf::class,
        ]);
    }
}
