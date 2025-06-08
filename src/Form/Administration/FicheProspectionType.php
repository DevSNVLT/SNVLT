<?php

namespace App\Form\Administration;

use App\Entity\Administration\FicheProspection;
use App\Entity\Autorisation\Attribution;
use App\Repository\Autorisations\AttributionRepository;
use App\Repository\References\ForetRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class FicheProspectionType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('fichier', FileType::class, [
                'label' =>false,  // $this->translator->trans('Upload a CSV file'),

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                'attr'=>[
                    'class'=>'monfichier',
                    'style'=>'
                                width: 350px;
                                max-width: 100%;
                                color: #444;
                                padding: 5px;
                                background: #fff;
                                border-radius: 10px;
                                border: 1px solid #555;'
                ],
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => true,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '3072k',
                       /* 'mimeTypes' => [
                            'text/csv'
                        ],*/
                        'maxSizeMessage'=> $this->translator->trans('Please, upload an CSV file with size less than 3 Mb'),
                       /* 'mimeTypesMessage' => $this->translator->trans('Please, upload a valid CSV file'),*/
                    ]),
                    new NotBlank ([
                        'message' => 'Merci de sélectionnez le fichier CSV'
                    ])

                ],
            ])
            ->add('code_attribution', EntityType::class, [
                'label'=>false,
                'class'=>Attribution::class,
                'attr'=>[
                    'class'=>'foret',
                    'style'=>'font-size: 16px;background-color: #fcf378; border: 1px solid lightgrey;width:300px;'
                ],
                'required'=>true,
                'constraints' => [
                    new NotBlank ([
                        'message' => 'Merci de sélectionnez la forêt',
                    ])
                ],
                'expanded'=>false,
                'multiple'=>false,
                'query_builder' => function (AttributionRepository $att): QueryBuilder {
                    return $att->createQueryBuilder('a')
                        ->andWhere('a.statut = true');
                }
            ])
            ->add('date_inventaire', DateType::class, [
                'label'=>false,
                'widget'=>'single_text',
                'html5'=>true,
                'attr'=>[
                    'style'=>'
                                max-width: 100%;
                                color: #444;
                                background-color: #fcf378;
                                border: 1px solid lightgrey;
                                width:100%;
                                height:24px;'
                ],
                'required'=>true,
                'constraints' => [
                    new NotBlank ([
                        'message' => 'La date de votre inventaire ne peut etre nulle',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FicheProspection::class,
        ]);
    }
}
