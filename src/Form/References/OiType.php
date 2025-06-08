<?php

namespace App\Form\References;

use App\Entity\References\Oi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class OiType  extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sigle', TextType::class, [
                'label'=>"Sigle",
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;'
                ]
            ])

            ->add('raisonSociale', TextType::class, [
                'label'=>"Raison sociale",
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark oi',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' =>  "La raison sociale est obligatoire",
                    ])
                ]
            ])

            ->add('code', TextType::class, [
                'label'=>"Code",
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;'
                ]
            ])
            ->add('adresse', TextType::class, [
                'label'=>'Adresse',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;'
                ]
            ])

            ->add('email', TextType::class, [
                'label'=>'email de l\'organisation',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow'
                ]
            ])

            ->add('personneRessource', TextType::class, [
                'label'=>$this->translator->trans('Cantonment manager'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark',
                    'style'=>'text-transform:uppercase;'
                ],

                'attr'=>[
                    'class'=>'form-control',
                    'style'=>'background-color:lightblue',
                    'readonly'=>true
                ]

            ])

            ->add('emailPersonneRessource', TextType::class, [
                'label'=>$this->translator->trans('Manager email'),
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
                'label'=>$this->translator->trans('Manager phone'),
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
            'data_class' => Oi::class,
        ]);
    }
}