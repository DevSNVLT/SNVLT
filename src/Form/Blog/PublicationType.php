<?php

namespace App\Form\Blog;

use App\Entity\Blog\CategoryPublication;
use App\Entity\Blog\Publication;
use App\Entity\Groupe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class PublicationType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libellePublication', TextType::class, [
                'label'=>$this->translator->trans('Titre de la publication'),
                'label_attr'=>[
                    'class'=>'font-weight-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control',
                    'style'=>'background-color:lightyellow;'
                ],
                'required'=>true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Le titre est obligatoire'),
                    ])
                ]
            ])
            ->add('actif', CheckboxType::class, [
                'label'=>$this->translator->trans('Actif ?'),
                'label_attr'=>[
                    'class'=>'font-weight-bold text-dark'
                ],
                'attr'=>[
                    /*'class'=>'form-control',*/
                    'style'=>'background-color:lightyellow;text-transform:uppercase;marging-left:25px;'
                ]
            ])
                ->add('codeCategory', EntityType::class,[
                    'label'=>"Catégorie",
                    'class'=>CategoryPublication::class,
                    'placeholder' => '-- Catégorie --',
                    'label_attr'=>[
                        'class'=>'font-weight-bold text-dark'
                    ],
                    'attr'=>[
                        'class'=>'form-control font-weight-bold;text-success',
                        'style'=>'background-color:lightyellow;'
                    ],

                    'multiple' => false,
                    'expanded' => false,
                    'required'=>true
                ])

            ->add('fichiers', FileType::class, [
                'label' => 'Charger vos fichier',
                'label_attr'=>[
                    'class'=>'font-weight-bold text-danger'
                ],
                'attr'=>[
                    /*'class'=>'form-control',*/
                    'style'=>'background-color:lightyellow;text-transform:uppercase;marging-left:25px;'
                ],
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                'multiple'=>true,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publication::class,
        ]);
    }
}