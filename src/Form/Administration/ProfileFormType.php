<?php

namespace App\Form\Administration;

use App\Entity\Groupe;
use App\Entity\References\Cantonnement;
use App\Entity\References\Ddef;
use App\Entity\References\Direction;
use App\Entity\References\Dr;
use App\Entity\References\Exploitant;
use App\Entity\References\Exportateur;
use App\Entity\References\PosteForestier;
use App\Entity\References\ServiceMinef;
use App\Entity\References\TypeOperateur;
use App\Entity\References\Usine;
use App\Entity\User;
use App\Repository\References\CantonnementRepository;
use App\Repository\References\DdefRepository;
use App\Repository\References\DirectionRepository;
use App\Repository\References\DrRepository;
use App\Repository\References\ExploitantRepository;
use App\Repository\References\ExportateurRepository;
use App\Repository\References\PosteForestierRepository;
use App\Repository\References\ServiceMinefRepository;
use App\Repository\References\UsineRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;


class ProfileFormType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_utilisateur', TextType::class, [
                'label'=>false,
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow; font-weight:bold',
                    'placeholder'=>$this->translator->trans('lastname')
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Please, type your lastname')
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => $this->translator->trans('Your lastname must have at least '). '{{ limit }}'.$this->translator->trans(' characters'),
                        // max length allowed by Symfony for security reasons
                        'max' => 255,
                    ]),
                ]
            ])
            ->add('prenoms_utilisateur', TextType::class, [
                'label'=>false,
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow; font-weight:bold',
                    'placeholder'=>$this->translator->trans('firstname')
                ],
                'constraints' => [
                    new NotBlank([
                        'message' =>  $this->translator->trans('Please, type your firstname')
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => $this->translator->trans('Your firstname must have at least '). '{{ limit }}'.$this->translator->trans(' characters'),
                        // max length allowed by Symfony for security reasons
                        'max' => 255,
                    ]),
                ]
            ])

            ->add('email', TextType::class, [
                'label'=>false,
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow; font-weight:bold',
                    'placeholder'=>$this->translator->trans('email')
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Please, type your email')
                    ])
                ]
            ])

            ->add('photo', FileType::class, [
                'label' => $this->translator->trans('Upload your image'),

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
            ->add('mobile', TextType::class, [
                'label'=>false,
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow; font-weight:bold',
                    'placeholder'=>$this->translator->trans('mobile phone')
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Please, type your mobile phone')
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => $this->translator->trans('Your mobile phone must have at least '). '{{ limit }}'.$this->translator->trans(' characters'),
                        // max length allowed by Symfony for security reasons
                        'max' => 14,
                        'maxMessage' => $this->translator->trans('Your mobile phone must not have more than '). '{{ limit }}'.$this->translator->trans(' characters')
                    ]),
                ]
            ])

        ;



    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
