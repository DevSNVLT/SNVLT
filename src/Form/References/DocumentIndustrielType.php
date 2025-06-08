<?php

namespace App\Form\References;

use App\Entity\References\DocumentOperateur;
use App\Entity\References\GrilleLegalite;
use App\Entity\References\TypeOperateur;
use App\Repository\References\DirectionRepository;
use App\Repository\References\GrilleLegaliteRepository;
use Doctrine\ORM\QueryBuilder;
use phpDocumentor\Reflection\Types\This;
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

class DocumentIndustrielType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code_document_grille', EntityType::class, [
                'label'=>'Nom du document',
                'class'=>GrilleLegalite::class,
                'required'=>true,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],

                'multiple'=>false,
                'expanded'=>false,
                'attr'=>[
                    'class'=>'form-control operateur alert-light text-dark',
                    'placeholder'=>'--Sélectionnez le document modèle...'
                ],

                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom du document est obligatoire',
                    ])
                ],
                'query_builder' => function (GrilleLegaliteRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('g')
                        ->where('g.code_operateur = 3' )
                        ->orderBy('g.libelle_document', 'ASC');
                }

            ])

            ->add('date_etablissement', DateType::class, [
                'label'=>'Date Etablissement',
                'required'=>true,
                'widget'=>'single_text',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark'
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
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark'
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
                    'class'=>'form-label mt-4'
                ],

                'attr'=>[
                    'class'=>'',

                ],
                'required'=>true,
                'constraints' => [
                    new File([
                        'maxSize' => '25000k',
                        'mimeTypes' => [
                            'application/pdf'
                        ],
                        'maxSizeMessage'=> 'SVP chargez une image de taille inférieure ou égale à 25Mo',
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
