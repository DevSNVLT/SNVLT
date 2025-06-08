<?php

namespace App\Form\Transformation;

use App\Entity\References\Essence;
use App\Entity\References\Pays;
use App\Entity\References\TypeTransformation;
use App\Entity\Transformation\Contrat;
use App\Entity\Transformation\TypeContrat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContratType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numeroContrat', TextType::class, [
                'label'=>$this->translator->trans('Contract No'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark text-center',
                    'style'=>'background-color:lightgreen;border:2px solid darkgreen; font-size:24px; font-weight: bold;width:200px;'
                ],
                'required'=>true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Contract No is mandatory'),
                    ])
                ]

            ])
            ->add('raisonSocialeClt', TextType::class, [
                'label'=>$this->translator->trans('Client Name'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightblue;text-transform:uppercase;'
                ],
                'required'=>true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Client Name is mandatory'),
                    ])
                ]

            ])
            ->add('personneResource', TextType::class, [
                'label'=>$this->translator->trans('Manager'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;'
                ],
                'required'=>true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Manager name is mandatory'),
                    ])
                ]

            ])

            ->add('emailPersonneRessource', TextType::class, [
                'label'=>$this->translator->trans('Manager email'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow;text-transform:lowercase;'
                ],
                'required'=>true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Manager name is mandatory'),
                    ])
                ]

            ])


            ->add('contactPersonneRessource', TextType::class, [
                'label'=>$this->translator->trans('Manager phone'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow'
                ],

            ])


            ->add('ville', TextType::class, [
                'label'=>$this->translator->trans('City'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;'
                ]

            ])

            ->add('adresse', TextType::class, [
                'label'=>$this->translator->trans('Address'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;'
                ]

            ])

            ->add('conditions', TextareaType::class, [
                'label'=>$this->translator->trans('Conditions'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow;height:100px;text-transform:uppercase;'
                ]

            ])

            ->add('dateContrat', DateType::class, [
                'label'=>$this->translator->trans('Contract Date'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow'
                ],
                'widget'=>'single_text',
                'html5'=>true,
                'required'=>true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Contract Date is mandatory'),
                    ])
                ]

            ])

            ->add('type_transfo', EntityType::class, [
                'label'=>'Type de transformation',
                'class'=> TypeTransformation::class,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-select',
                    'style'=>'background-color:lightyellow'
                ],
                'multiple'=>false,
                'expanded'=>false
            ])
            ->add('typeContrat', EntityType::class, [
                'label'=>'Type de contrat',
                'class'=> TypeContrat::class,

                'label_attr'=>[
                    'class'=>'fw-bold text-dark',
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightpink; font-size:16px; font-weight: bold;width:200px;'
                ],
                'multiple'=>false,
                'expanded'=>false
            ])
            ->add('pays', EntityType::class, [
                'label'=>$this->translator->trans('Country'),
                'placeholder'=>$this->translator->trans('Country list'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'class'=>Pays::class,
                'multiple'=>false,
                'expanded'=>false,
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow'
                ]

            ])

            ->add('destinationColis', TextType::class, [
                'label'=>$this->translator->trans('Destination'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;'
                ]

            ])

            ->add('volumeDemande', NumberType::class, [
                'label'=>$this->translator->trans('Requested Volume'). " m3",
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow'
                ]

            ])

            ->add('essence', EntityType::class, [
                'label'=>$this->translator->trans('Species'),
                'class'=> Essence::class,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark '
                ],
                'attr'=>[
                    'class'=>'choices form-select multiple-remove',
                    'style'=>'background-color:lightyellow'
                ],
                'multiple'=>true,
                'expanded'=>false,
                'required'=>true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contrat::class,
        ]);
    }
}