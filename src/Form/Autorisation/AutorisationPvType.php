<?php

namespace App\Form\Autorisation;

use App\Entity\Autorisation\AttributionPv;
use App\Entity\Autorisation\AutorisationPv;
use App\Entity\References\Exploitant;
use App\Repository\Autorisation\AutorisationPvRepository;
use App\Repository\Autorisations\AttributionPvRepository;
use App\Repository\References\ExploitantRepository;
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

class AutorisationPvType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero_autorisation', TextType::class, [
                'label'=>$this->translator->trans('Authorization No'),
                'required'=>true,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle numero_dossier',
                    'style'=>'background-color:lightpink; font-size:20px; font-weight:bold;'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Authorization No is mandatory')
                    ])
                ]
            ])
            ->add('date_autorisation', DateType::class, [
                'label'=>$this->translator->trans('Auth. Date'),
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
                        'message' => $this->translator->trans('Authorization Date is mandatory')
                    ])
                ]

            ])

            ->add('debutValidite', DateType::class, [
                'label'=>$this->translator->trans('Start Date'),
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
                        'message' => $this->translator->trans('Start Date is mandatory')
                    ])
                ]

            ])
            ->add('finValidite', DateType::class, [
                'label'=>$this->translator->trans('End Date'),
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
                        'message' => $this->translator->trans('End Date is mandatory')
                    ])
                ]

            ])


            ->add('code_attribution_pv', EntityType::class, [
                'label'=>$this->translator->trans('Owner'),
                'class'=>AttributionPv::class,
                'required'=>true,
                'multiple'=>false,
                'expanded'=>false,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'query_builder' => function (AttributionPvRepository $att): QueryBuilder {
                    return $att->createQueryBuilder('a')
                        ->andWhere('a.statut = true')
                        ->orderBy('a.date_decision', 'DESC');
                },
                'attr'=>[
                    'class'=>'form-control code_attributaire_pv alert-primary font-weight-bold'
                ]

            ])

            ->add('code_exploitant', EntityType::class, [
                'label'=>$this->translator->trans('Logger'),
                'class'=>Exploitant::class,
                'required'=>true,
                'multiple'=>false,
                'expanded'=>false,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'query_builder' => function (ExploitantRepository $type_dossier): QueryBuilder {
                    return $type_dossier->createQueryBuilder('t')
                        ->andWhere('t.email_personne_ressource is not null')
                        ->orderBy('t.raison_sociale_exploitant', 'ASC');
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
            'data_class' => AutorisationPv::class
        ]);
    }
}