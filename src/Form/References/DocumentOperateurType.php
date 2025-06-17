<?php

namespace App\Form\References;

use App\Entity\References\DocumentOperateur;
use App\Entity\References\GrilleLegalite;
use App\Entity\References\TypeOperateur;
use App\Repository\References\DdefRepository;
use App\Repository\References\GrilleLegaliteRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

class DocumentOperateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code_document_grille', EntityType::class, [
                'label'=>'Nom du document',
                'class'=>GrilleLegalite::class,
                'required'=>true,
                'label_attr'=>[
                    'class'=>'font-weight-bold',
                    'style'=>'color:orangered;'
                ],

                'multiple'=>false,
                'expanded'=>false,
                'attr'=>[
                    'class'=>'form-control alert-secondary doc_operateur font-weight-bold text-dark',
                    'placeholder'=>'--Sélectionnez le document modèle...',
                    'style'=>'font-size:18px;'
                ],

                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom du document est obligatoire',
                    ])
                ],
                'query_builder' => function (GrilleLegaliteRepository $gr): QueryBuilder {
                    return $gr->createQueryBuilder('g')
                        ->where('g.code_operateur = 2')
                        ->orderBy('g.libelle_document', 'ASC');
                }

            ])
            ->add('description', TextareaType::class, [
                'label'=>'Description',
                'label_attr'=>[
                    'class'=>'font-weight-bold',
                    'style'=>'color:orangered;'
                ],

                'attr'=>[
                    'class'=>'form-control text-dark description',
                    'style'=>'min-height:300px;font-size:14px;;background:white;',
                    'readonly'=>true
                ],
            ])

            ->add('date_etablissement', DateType::class, [
                'label'=>'Date Etablissement',
                'required'=>true,
                'widget'=>'single_text',
                'label_attr'=>[
                    'class'=>'font-weight-bold',
                    'style'=>'color:orangered;'
                ],
                'attr'=>[
                    ' class'=>'form-control text-dark date_etablissement',
                    'style'=>'font-size:18px;;background:lightyellow'
                ],

                'constraints' => [
                    new NotBlank([
                        'message' => 'La date d\'établissement du document est obligatoire',
                    ])
                ]

            ])
            ->add('date_expiration', DateType::class, [
                'label'=>'Date Expiration',
                'required'=>true,
                'widget'=>'single_text',
                'label_attr'=>[
                    'class'=>'font-weight-bold',
                    'style'=>'color:orangered;'
                ],
                'attr'=>[
                    'class'=>'form-control text-dark date_expiration',
                    'style'=>'font-size:18px;;background:lightyellow'
                ],

                'constraints' => [
                    new NotBlank([
                        'message' => 'La date d\'expiration du document est obligatoire',
                    ])
                ]

            ])
            ->add('imageFile', VichImageType::class,[
                'label'=>'Ficher',
                'allow_file_upload'=>true,
                'allow_delete'=>true,
                'error_bubbling'=>true,
                'download_uri'=>true,

                'label_attr'=>[
                    'class'=>'font-weight-bold  form-label mt-4',
                    'style'=>'color:orangered;'
                ],

                'attr'=>[
                    'class'=>'image_file text-danger',
                    'style'=>'font-size:18px;background:lightyellow'

                ],
                'required'=>true,
                'constraints' => [
                    new File([
                        'maxSize' => '5124k',
                        'mimeTypes' => [
                            'application/pdf'
                        ],
                        'maxSizeMessage'=> 'SVP chargez une image de taille inférieure ou égale à 5Mo',
                        'mimeTypesMessage' => 'SVP chargez un document PDF valide',
                    ])

    ],

            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DocumentOperateur::class,
        ]);
    }
}
