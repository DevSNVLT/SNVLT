<?php

namespace App\Form\References;

use App\Entity\References\Dr;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class DrType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('denomination', TextType::class, [
                'label'=>$this->translator->trans('RD Name'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('RD Name is mandatory'),
                    ])
                ]
            ])


            ->add('personneRessource', TextType::class, [
                'label'=>$this->translator->trans('Cantonment manager'),
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
                'label'=>$this->translator->trans('Cantonment manager email'),
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
                'label'=>$this->translator->trans('Cantonment manager phone'),
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
            'data_class' => Dr::class,
        ]);
    }
}
