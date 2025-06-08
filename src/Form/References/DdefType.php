<?php

namespace App\Form\References;

use App\Entity\References\Ddef;
use App\Entity\References\Dr;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class DdefType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_ddef', TextType::class, [
                'label'=>$this->translator->trans('DD Name'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow'
                ],
                'required'=>true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('DD Name is mandatory'),
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


            ->add('code_dr', EntityType::class, [
                'label'=>'Sélectionner la Direction Régionale',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'class'=> Dr::class,
                'multiple'=>false,
                'expanded'=>false,
                'attr'=>[
                    '           class'=>'form-control code_dr alert-light text-dark'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('RD Name is mandatory'),
                    ])

                ],

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ddef::class,
        ]);
    }
}
