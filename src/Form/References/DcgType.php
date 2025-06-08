<?php

namespace App\Form\References;

use App\Entity\References\Dcg;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class DcgType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('denomination', TextType::class, [
                'label'=>'Dénomination',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow',
                    'placeholder'=>'Saisissez SVP le centre de gestion'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'La dénomination est obligatoire',
                    ])
                ]
            ])


            ->add('personneRessource', TextType::class, [
                'label'=>'Responsable DCG',
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
                'label'=>'Email responsable',
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
                'label'=>'Mobile Responsable',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
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
            'data_class' => Dcg::class,
        ]);
    }
}
