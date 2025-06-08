<?php

namespace App\Form\References;

use App\Entity\References\Imprimeur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImprimeurType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codeImprimeur', TextType::class, [
                'label'=>$this->translator->trans('Code'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow'
                ]
            ])

            ->add('raisonSocialeImprimeur', TextType::class, [
                'label'=>$this->translator->trans('Name'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark direction',
                    'style'=>'background-color:lightyellow'
                ],
                'required'=>true,
                'constraints' => [
                    new NotBlank([
                        'message' =>  $this->translator->trans('The direction name is mandatory'),
                    ])
                ]
            ])

            ->add('adresse', TextType::class, [
                'label'=>$this->translator->trans('Address'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],

                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow'
                ]

            ])

            ->add('ville', TextType::class, [
                'label'=>$this->translator->trans('city'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow'
                ]

            ])


            ->add('logo', FileType::class, [
                'label' => $this->translator->trans('Upload logo'),

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                'attr'=>[
                    'class'=>'btn btn-primary',
                    'style'=>'background-color:lightyellow; font-weight:bold'
                ],
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                        ],
                        'maxSizeMessage'=> $this->translator->trans('Please, upload an image with size less than 1 Mb'),
                        'mimeTypesMessage' => $this->translator->trans('Please, upload a valid image'),
                    ])

                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Imprimeur::class,
        ]);
    }
}