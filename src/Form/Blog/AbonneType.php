<?php

namespace App\Form\Blog;

use App\Entity\Blog\Abonne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class AbonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label'=>'votre nom',
                'label_attr'=>[
                    'class'=>'fw-bold text-primary fs-2'
                ],
                'attr'=>[
                    'class'=>'form-control mb-3'
                ]
            ])
            ->add('prenoms', TextType::class, [
                'label'=>'vos prénoms',
                'label_attr'=>[
                    'class'=>'fw-bold text-primary fs-2'
                ],
                'attr'=>[
                    'class'=>'form-control mb-3'
                ]
                ])
            ->add('telephone', TextType::class, [
                'label'=>'votre téléphone',
                'label_attr'=>[
                    'class'=>'fw-bold text-primary fs-2'
                ],
                'attr'=>[
                    'class'=>'form-control mb-3'
                ]
                ])
            ->add('email', TextType::class, [
                'label'=>'votre email',
                'label_attr'=>[
                    'class'=>'fw-bold text-primary fs-2'
                ],
                'attr'=>[
                    'class'=>'form-control mb-3'
                ]
                ])
            ->add('newsletter', CheckboxType::class, [
                'label'=>'Voulew-vous recevoir notre newsletter ?',
                'label_attr'=>[
                    'class'=>'fw-bold text-danger fs-2'
                ],
                'attr'=>[
                    'class'=>'form-select mb-3'
                ]
                ])
            ->add('pseudo', TextType::class, [
                'label'=>'votre pseudo',
                'label_attr'=>[
                    'class'=>'fw-bold text-primary fs-2'
                ],
                'attr'=>[
                    'class'=>'form-control mb-3'
                ]
                ])
            ->add('avatar', FileType::class, [
                'label' => 'Insérer votre photo ',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                'attr'=> [
                    'class'=>'form-control form-control-sm mb-2 alert-secondary text-primary fw-light'
                ],

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
                        'maxSizeMessage'=> 'SVP chargez une image de taille inférieure ou égale à 1Mo',
                        'mimeTypesMessage' => 'SVP chargez une image valide',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Abonne::class,
        ]);
    }
}
