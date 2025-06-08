<?php

namespace App\Form\Administration;

use App\Entity\References\Cantonnement;
use App\Entity\References\Direction;
use App\Entity\References\Dr;
use App\Entity\References\Exploitant;
use App\Entity\References\Exportateur;
use App\Entity\References\PosteForestier;
use App\Entity\References\ServiceMinef;
use App\Entity\References\TypeOperateur;
use App\Entity\References\Usine;
use App\Entity\User;
use App\Entity\References\Ddef;
use App\Repository\References\CantonnementRepository;
use App\Repository\References\DdefRepository;
use App\Repository\References\DirectionRepository;
use App\Repository\References\ExploitantRepository;
use App\Repository\References\ExportateurRepository;
use App\Repository\References\PosteForestierRepository;
use App\Repository\References\ServiceMinefRepository;
use App\Repository\References\UsineRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use VictorPrdh\RecaptchaBundle\Form\ReCaptchaType;

class RegisterMemberFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_utilisateur', TextType::class, [
                'label'=>false,
                'required'=>true,
                'attr' => [
                    'class'=>'input--style-1 w-100',
                    'placeholder'=>'Nom'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner votre nom',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre nom doit avoir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 255,
                    ]),
                ]
            ])
            ->add('prenoms_utilisateur', TextType::class, [
                'label'=>false,
                'required'=>true,
                'attr' => [
                    'class'=>'input--style-1',
                    'placeholder'=>'Prénom(s)'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner votre prénoms',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre prénom doit avoir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 255,
                    ]),
                ]
            ])

            ->add('email', TextType::class, [
                'label'=>false,
                'required'=>true,
                'attr' => [
                    'class'=>'input--style-1',
                    'placeholder'=>'Email'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner votre email',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre nom doit avoir au moins  {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 255,
                    ]),
                ]
            ])

            ->add('mobile', TextType::class, [
                'label'=>false,
                'required'=>true,
                'attr' => [
                    'class'=>'input--style-1',
                    'placeholder'=>'Téléphone mobile'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner votre numéro de téléphone',
                    ]),
                    new Length([
                        'min' => 14,
                        'minMessage' => 'Votre N° de téléphone doit avoir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 19,
                        'maxMessage' => 'Votre N° de téléphone doit avoir au maximum {{ limit }} caractères',
                    ]),
                ]
            ])
            ->add('code_operateur', EntityType::class,[
                'label'=>false,
                'class'=>TypeOperateur::class,
                'placeholder' => '-- Vous êtes --',
                'attr'=>[
                    'class'=>'select2 role_user'
                ],

                'multiple' => false,
                'expanded' => false,
                'required'=>true
            ])

            ->add('codeexploitant', EntityType::class,[
                'label'=>false,
                'class'=>Exploitant::class,

                'placeholder' => '-- Exploitants Forestiers --',
                'attr'=>[
                    'class'=>'select2 code_exploitant'
                ],
                'choice_label' => 'raison_sociale_exploitant',
                'multiple' => false,
                'expanded' => false,
                'required'=>false,
                'query_builder' => function (ExploitantRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('e')
                        ->where('e.email_personne_ressource is not null')
                        ->orderBy('e.raison_sociale_exploitant', 'ASC');
                }

            ])

            ->add('codeindustriel', EntityType::class,[
                'label'=>false,
                'class'=>Usine::class,
                'placeholder' => '-- Transformateurs du bois --',
                'attr'=>[
                    'class'=>'select2 code_industriel'
                ],
                'choice_label' => 'raison_sociale_usine',
                'multiple' => false,
                'expanded' => false,
                'required'=>false,
                'query_builder' => function (UsineRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('u')
                        ->where('u.email_personne_ressource is not null')
                        ->orderBy('u.raison_sociale_usine', 'ASC');
                }
            ])

            ->add('code_dr', EntityType::class,[
                'label'=>false,
                'class'=>Dr::class,
                'placeholder' => '-- Directions Régionale --',
                'attr'=>[
                    'class'=>'select2 code_dr'
                ],
                'choice_label' => 'denomination',
                'multiple' => false,
                'expanded' => false,
                'required'=>false
            ])

            ->add('code_cantonnement', EntityType::class,[
                'label'=>false,
                'class'=>Cantonnement::class,
                'placeholder' => '-- Cantonnements Forestiers --',
                'attr'=>[
                    'class'=>'select2 code_cantonnement'
                ],
                'choice_label' => 'nom_cantonnement',
                'multiple' => false,
                'expanded' => false,
                'required'=>false,
                'query_builder' => function (CantonnementRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('c')
                        ->where('c.email_personne_ressource is not null')
                        ->orderBy('c.nom_cantonnement', 'ASC');
                }
            ])

            ->add('code_ddef', EntityType::class,[
                'label'=>false,
                'class'=>Ddef::class,
                'placeholder' => '-- Direction départementale --',
                'attr'=>[
                    'class'=>'select2 ddef'
                ],

                'multiple' => false,
                'expanded' => false,
                'required'=>false,
                'query_builder' => function (DdefRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('d')
                        ->where('d.email_personne_ressource is not null')
                        ->orderBy('d.nom_ddef', 'ASC');
                }
            ])

            ->add('code_poste_controle', EntityType::class,[
                'label'=>false,
                'class'=>PosteForestier::class,
                'placeholder' => '-- Poste Forestier --',
                'attr'=>[
                    'class'=>'select2 pf'
                ],

                'multiple' => false,
                'expanded' => false,
                'required'=>false,
                'query_builder' => function (PosteForestierRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('p')
                        ->where('p.email_personne_ressource is not null')
                        ->orderBy('p.denomination', 'ASC');
                }
            ])

            ->add('code_direction', EntityType::class,[
                'label'=>false,
                'class'=>Direction::class,
                'placeholder' => '-- Directions Minef --',
                'attr'=>[
                    'class'=>'select2 direction_minef'
                ],
                'choice_label' => 'sigle',
                'multiple' => false,
                'expanded' => false,
                'required'=>false,
                'query_builder' => function (DirectionRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('d')
                        ->where('d.email_personne_ressource is not null')
                        ->orderBy('d.denomination', 'ASC');
                }
            ])
            ->add('code_service', ChoiceType::class, [
                'label'=>false,
                'placeholder'=>'Sélectionner dabord une direction',
                'attr'=>[
                    'class'=>'select2 service_minef'
                ]
            ])

            ->add('code_exportateur', EntityType::class,[
                'label'=>false,
                'class'=>Exportateur::class,
                'placeholder' => '-- Exportateurs du bois --',
                'attr'=>[
                    'class'=>'select2 exportateur'
                ],

                'multiple' => false,
                'expanded' => false,
                'required'=>false,
                'query_builder' => function (ExportateurRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('e')
                        ->where('e.email_personne_ressource is not null')
                        ->orderBy('e.raison_sociale_exportateur', 'ASC');
                },
            ])

           /* ->add('captcha', RecaptchaType::class)*/
        ;

        $formModifier = function (FormInterface $form, Direction $code_direction = null){
            $services_minef = (null === $code_direction) ? [] : $code_direction->getServiceMinefs();
            $form->add('code_service', EntityType::class, [
                'class'=>ServiceMinef::class,
                'choices'=>$services_minef,
                'choice_label'=>'libelle_service',
                'placeholder'=>'Sélectionnez SVP un service',
                'label'=>'Services',
                'query_builder' => function (ServiceMinefRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('s')
                        ->where('s.email_personne_ressource is not null')
                        ->orderBy('s.libelle_service', 'ASC');
                }

            ]);
        };

        $builder->get('code_direction')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier){
                $code_direction = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $code_direction);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}