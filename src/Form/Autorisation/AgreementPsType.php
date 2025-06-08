<?php

namespace App\Form\Autorisation;

use App\Entity\Autorisation\AgreementPs;
use App\Entity\Autorisation\Attribution;
use App\Entity\References\AttributairePs;
use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Entity\References\TypeDossierPs;
use App\Repository\References\AttributairePsRepository;
use App\Repository\References\ExploitantRepository;
use App\Repository\References\ForetRepository;
use App\Repository\References\TypeDossierPsRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class AgreementPsType extends  AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero_dossier', TextType::class, [
                'label'=>$this->translator->trans('Folder No'),
                'required'=>true,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle numero_dossier',
                    'style'=>'background-color:lightpink; font-size:20px; font-weight:bold;',
                    'readonly'=>true
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Folder No is mandatory')
                    ])
                ]
            ])
            ->add('dateOuverture', DateType::class, [
                'label'=>$this->translator->trans('Open Date'),
                'widget'=>'single_text',
                'required'=>true,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Open Date is mandatory')
                    ])
                ]

            ])
            ->add('montant_agrement', NumberType::class, [
                'label'=>$this->translator->trans('Agreement amount'),
                'required'=>true,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control montant ',
                    'style'=>'background-color:lightyellow; font-size:32px; font-weight:bold;',
                    'readonly'=>true
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Agreement amount is mandatory')
                    ])
                ]

            ])
            ->add('code_attributaire_ps', EntityType::class, [
                'label'=>$this->translator->trans('Owner'),
                'class'=>AttributairePs::class,
                'required'=>true,
                'multiple'=>false,
                'expanded'=>false,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'query_builder' => function (AttributairePsRepository $att): QueryBuilder {
                    return $att->createQueryBuilder('a')
                        ->andWhere('a.statut = true')
                        ->orderBy('a.raison_sociale', 'ASC');
                },
                'attr'=>[
                    'class'=>'form-control code_attributaire_ps alert-primary font-weight-bold'
                ]

            ])

            ->add('code_type_dossier', EntityType::class, [
                'label'=>$this->translator->trans('Activity Type'),
                'class'=>TypeDossierPs::class,
                'required'=>true,
                'multiple'=>false,
                'expanded'=>false,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'query_builder' => function (TypeDossierPsRepository $type_dossier): QueryBuilder {
                    return $type_dossier->createQueryBuilder('t')
                        ->andWhere('t.montant_agreement is not null')
                        ->orderBy('t.libelle', 'ASC');
                },
                'attr'=>[
                    '           class'=>'form-control text-sm text-sm text-dark type_dossier alert-primary font-weight-bold'
                ]

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AgreementPs::class
        ]);
    }
}