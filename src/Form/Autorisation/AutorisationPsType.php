<?php

namespace App\Form\Autorisation;

use App\Entity\Autorisation\AgreementPs;
use App\Entity\Autorisation\AutorisationPs;
use App\Entity\References\NaturePs;
use App\Repository\Autorisations\AgreementPsRepository;
use App\Repository\References\NaturePsRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class AutorisationPsType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero_auto_ps', TextType::class, [
                'label'=>$this->translator->trans('PS Authorization No'),
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
                        'message' => $this->translator->trans('PS Authorization No is mandatory')
                    ])
                ]
            ])
            ->add('date_delivrance', DateType::class, [
                'label'=>$this->translator->trans('PS Authorization Delivery Date'),
                'widget'=>'single_text',
                'required'=>true,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle date_delivrance',
                    'style'=>'background-color:lightyellow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('date is mandatory')
                    ])
                ]

            ])
            ->add('date_expiration', DateType::class, [
                'label'=>$this->translator->trans('PS Authorization  Date'),
                'widget'=>'single_text',
                'required'=>true,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle date_expiration',
                    'style'=>'background-color:lightyellow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Open Date date is mandatory')
                    ])
                ]

            ])
            ->add('montant_autorisation', NumberType::class, [
                'label'=>$this->translator->trans('Authorization amount'),
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
                        'message' => $this->translator->trans('Authorization amount is mandatory')
                    ])
                ]

            ])
            ->add('code_dossier', EntityType::class, [
                'label'=>$this->translator->trans('Owner'),
                'class'=>AgreementPs::class,
                'required'=>true,
                'multiple'=>false,
                'expanded'=>false,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control code_dossier alert-primary font-weight-bold'
                ],
                'query_builder' => function (AgreementPsRepository $agreementPs): QueryBuilder {
                    return $agreementPs->createQueryBuilder('a')
                        ->andWhere('a.reprise = false')
                        ->orderBy('a.numero_dossier', 'ASC');
                }

            ])

            ->add('code_produit', EntityType::class, [
                'label'=>$this->translator->trans('Product Nature'),
                'class'=>NaturePs::class,
                'required'=>true,
                'multiple'=>false,
                'expanded'=>false,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'query_builder' => function (NaturePsRepository $natureps): QueryBuilder {
                    return $natureps->createQueryBuilder('n')
                        ->andWhere('n.montant_autorisation is not null')
                        ->orderBy('n.libelle', 'ASC');
                },
                'attr'=>[
                    '           class'=>'form-control text-sm text-sm text-dark code_produit alert-primary font-weight-bold'
                ]

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AutorisationPs::class
        ]);
    }

}