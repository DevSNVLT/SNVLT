<?php

namespace App\Form\Observateur;


use App\Entity\Groupe;
use App\Entity\Observateur\PublicationRapport;
use App\Entity\Observateur\Ticket;
use App\Entity\References\Cantonnement;
use App\Entity\References\Direction;
use App\Entity\References\Dr;
use App\Entity\References\Foret;
use App\Entity\References\ServiceMinef;
use App\Repository\GroupeRepository;
use App\Repository\References\CantonnementRepository;
use App\Repository\References\DdefRepository;
use App\Repository\References\DirectionRepository;
use App\Repository\References\DrRepository;
use App\Repository\References\ForetRepository;
use App\Repository\References\ServiceMinefRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class PublicationRapportType extends AbstractType
{
    public  function __construct(private TranslatorInterface $translator)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, [
                'label'=>'Sujet <span class="text-danger">*</span>',
                'label_html' => true,
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow; font-weight:bold'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Renseignez SVP le sujet')
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => $this->translator->trans('le libellé ou Sujet doiot avoir au moins '). '{{ limit }}'.$this->translator->trans(' characters'),
                        // max length allowed by Symfony for security reasons
                        'max' => 255,
                    ]),
                ]
            ])
            ->add('resume', TextareaType::class, [
                'label'=>'Message <span class="text-danger">*</span>',
                'label_html'=>true,
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow;height:250px;'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Merci d\'adresser un message')
                    ]),
                    new Length([
                        'min' => 20,
                        'minMessage' => $this->translator->trans('Votre message doit comporter au moins '). '{{ limit }}'.$this->translator->trans(' caractères'),
                        // max length allowed by Symfony for security reasons
                        'max' => 300,
                        'maxMessage' => $this->translator->trans('Votre message doit comporter au plus'). '{{ limit }}'.$this->translator->trans(' caractères')
                    ]),
                ]
            ])
            
            ->add('fichier', FileType::class, [
                'label' => 'Charger votre fichier <span class="text-danger">*</span>',
                'label_html' => true,
                'mapped' => false,

                'required' => false,

                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Merci de charger un rapport')
                    ]),
                    new File([
                        'maxSize' => '51000k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                        ],
                        'maxSizeMessage'=> "SVP, Chargez un fichier de moins de  50 Mb",
                        'mimeTypesMessage' =>"SVP, Chargez un fichier valide"
                    ])

                ],
            ])
            ->add('code_dr', EntityType::class, [
                'label'=>'Directions Régionales concernées',
                'class'=> Dr::class,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'choices form-select multiple-remove',
                    'style'=>'background-color:lightyellow'
                ],
                'multiple'=>true,
                'expanded'=>false,
                'query_builder' => function (DrRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('d')
                        ->where('d.email_personne_ressource is not null')
                        ->orderBy('d.denomination', 'ASC');
                }
            ])
            
            
            ->add('code_cef', EntityType::class, [
                'label'=>'Cantonnements',
                'class'=> Cantonnement::class,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'choices form-select multiple-remove',
                    'style'=>'background-color:lightyellow;display:none;',
                    'id'=>'code_cef'
                ],
                'multiple'=>true,
                'expanded'=>false,
                'query_builder' => function (CantonnementRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('c')
                        ->where('c.email_personne_ressource is not null')
                        ->orderBy('c.nom_cantonnement', 'ASC');
                }
            ])
            ->add('codeforet', EntityType::class, [
                'class' => Foret::class,
                'label'=>'Forêts Classées',
                'choice_label' => 'denomination',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'choices form-select multiple-remove',
                    'style'=>'background-color:lightyellow;display:none;',
                    'id'=>'codeforet'
                ],
                'multiple' => true,
                'query_builder' => function (ForetRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('f')
                        ->where('f.code_type_foret = 2')
                        ->orderBy('f.denomination', 'ASC');
                }
            ])
            ->add('code_direction', EntityType::class, [
                'label'=>'Directions MINEF',
                'class'=> Direction::class,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'choices form-select multiple-remove',
                    'style'=>'background-color:lightyellow;display:none;',
                    'id'=>'code_direction'
                ],
                'multiple'=>true,
                'expanded'=>false,
                'query_builder' => function (DirectionRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('d')
                        ->where('d.email_personne_ressource is not null')
                        ->orderBy('d.denomination', 'ASC');
                }
            ])

            ->add('code_service_minef', EntityType::class, [
                'label'=>'Services MINEF',
                'class'=> ServiceMinef::class,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'choices form-select multiple-remove',
                    'style'=>'background-color:lightyellow;display:none;',
                    'id'=>'code_service'
                ],
                'multiple'=>true,
                'expanded'=>false,
                'query_builder' => function (ServiceMinefRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('s')
                        ->where('s.email_personne_ressource is not null')
                        ->orderBy('s.libelle_service', 'ASC');
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PublicationRapport::class,
        ]);
    }
}