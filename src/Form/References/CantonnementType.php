<?php

namespace App\Form\References;

use App\Entity\References\Cantonnement;
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

class CantonnementType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_cantonnement', TextType::class, [
                'label'=>"Dénomination",
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;'
                ],
                'required'=>true,
                'constraints' => [
                    new NotBlank([
                        'message' => "le nom du cantonnement est obligatoire",
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


            ->add('code_ddef', EntityType::class, [
                'label'=>$this->translator->trans('Select Departmental Direction'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'placeholder'=>$this->translator->trans('Select Departmental Direction...'),
                'class'=> Ddef::class,
                'multiple'=>false,
                'expanded'=>false,
                'attr'=>[
                    'class'=>'form-control code_ddef alert-light text-dark',
                    'placeholder'=>$this->translator->trans('Select Departmental Direction...'),
                ]

            ])

            ->add('code_dr', EntityType::class, [
                'label'=>$this->translator->trans('Select Regional Direction...'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'class'=> Dr::class,
                'multiple'=>false,
                'expanded'=>false,
                'placeholder'=>$this->translator->trans('Select Regional Direction...'),
                'attr'=>[
                    ' class'=>'form-control code_dr',
                    'placeholder'=>$this->translator->trans('Select Regional Direction...')
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Regional Direction is madatory'),
                    ])

                ],

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cantonnement::class,
        ]);
    }
}
