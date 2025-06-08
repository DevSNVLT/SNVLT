<?php

namespace App\Form\Observateur;

use App\Entity\Groupe;
use App\Entity\Observateur\Ticket;
use App\Entity\References\Cantonnement;
use App\Entity\References\Direction;
use App\Entity\References\Dr;
use App\Entity\References\ServiceMinef;
use App\Entity\User;
use App\Repository\GroupeRepository;
use App\Repository\References\CantonnementRepository;
use App\Repository\References\DirectionRepository;
use App\Repository\References\DrRepository;
use App\Repository\References\ServiceMinefRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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

class TicketType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sujet', TextType::class, [
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
                        'minMessage' => $this->translator->trans('Your lastname must have at least '). '{{ limit }}'.$this->translator->trans(' characters'),
                        // max length allowed by Symfony for security reasons
                        'max' => 255,
                    ]),
                ]
            ])
            ->add('message_text', TextareaType::class, [
                'label'=>'Message <span class="text-danger">*</span>',
                'label_html' => true,
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow; font-weight:bold;height:200px;'
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

            ->add('statut', ChoiceType::class, [
                'label'=>'Niveau Urgence <span class="text-danger">*</span>',
                'label_html' => true,
                'required'=>true,
                'choices'=>[
                    'AUCUN'=>'AUCUN',
                    'MOYEN'=>'MOYEN',
                    'ALARMANT'=>'ALARMANT',
                    'CRITIQUE'=>'CRITIQUE',
                ],
                'attr'=>[
                    'class'=>'form-control sigle alert-danger'
                ]
            ])

            ->add('codeCantonnement', EntityType::class, [
                'label'=>'Cantonnement',
                'class'=> Cantonnement::class,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'choices form-select multiple-remove',
                    'style'=>'background-color:lightyellow',
                ],
                'multiple'=>false,
                'expanded'=>false,
                'query_builder' => function (CantonnementRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('c')
                        ->where('c.email_personne_ressource is not null')
                        ->orderBy('c.nom_cantonnement', 'ASC');
                }
            ])
            ->add('x', NumberType::class, [
                'label'=>false,
                'required'=>true,

                'attr'=>[
                    'class'=>'form-control sigle alert-lightyellow m-2 text-center',
                    'style'=>'background-color:lightyellow;font-size:22px;'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('La coordonnée X est obligatoire')
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => $this->translator->trans('Saisir '). '{{ limit }}'.$this->translator->trans(' chiffres'),
                        // max length allowed by Symfony for security reasons
                        'max' => 6,
                        'maxMessage' => $this->translator->trans('Saisir '). '{{ limit }}'.$this->translator->trans(' chiffres'),
                    ]),
                ]
            ])
            ->add('y', NumberType::class, [
                'label'=>false,
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control sigle alert-lightyellow m-2 text-center',
                    'style'=>'background-color:lightyellow;font-size:22px;'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('La coordonnée Y est obligatoire')
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => $this->translator->trans('Saisir '). '{{ limit }}'.$this->translator->trans(' chiffres'),
                        // max length allowed by Symfony for security reasons
                        'max' => 6,
                        'maxMessage' => $this->translator->trans('Saisir '). '{{ limit }}'.$this->translator->trans(' chiffres'),
                    ]),
                ]
            ])
            ->add('fichiers', FileType::class, [
                'label' => 'Charger vos fichiers <span class="text-danger">*</span>',
                'label_html' => true,
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

            ->add('code_dr', EntityType::class, [
                'label'=>'DR',
                'class'=> Dr::class,

                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'choices form-select multiple-remove',
                    'style'=>'background-color:lightyellow;display:none;',
                    'placeholder'=>'DR'
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
                    'style'=>'background-color:lightyellow;display:none;'
                ],
                'multiple'=>true,
                'expanded'=>false,
                'query_builder' => function (CantonnementRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('c')
                        ->where('c.email_personne_ressource is not null')
                        ->orderBy('c.nom_cantonnement', 'ASC');
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

            ->add('code_service', EntityType::class, [
                'label'=>'Services MINEF',
                'class'=> ServiceMinef::class,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'choices form-select multiple-remove',
                    'style'=>'background-color:lightyellow;display:none;'
                ],
                'multiple'=>true,
                'expanded'=>false,
                'query_builder' => function (ServiceMinefRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('s')
                        ->where('s.email_personne_ressource is not null')
                        ->orderBy('s.libelle_service', 'ASC');
                }
            ])

            /*->add('code_users', EntityType::class, [
                'label'=>'Utilisateurs ',
                'class'=> User::class,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'choices form-select multiple-remove',
                    'style'=>'background-color:lightyellow'
                ],
                'multiple'=>true,
                'expanded'=>false,
                'query_builder' => function (UserRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('u')
                        ->where('u.actif = true')
                        ->orderBy('u.nom_utilisateur', 'ASC');
                }
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
