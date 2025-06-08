<?php

namespace App\Form\Autorisation;

use App\Entity\Autorisation\AttributionPv;
use App\Entity\Autorisation\AutorisationPv;
use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Repository\Autorisations\AttributionPvRepository;
use App\Repository\References\ExploitantRepository;
use App\Repository\References\ForetRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class AttributionPvType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero_decision', TextType::class, [
                'label'=>$this->translator->trans('Decision  No'),
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
                        'message' => $this->translator->trans('Decision No is mandatory')
                    ])
                ]
            ])
            ->add('date_decision', DateType::class, [
                'label'=>$this->translator->trans('Decision  Date'),
                'widget'=>'single_text',
                'html5'=>true,

                'required'=>true,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow',
                    ' inputFormat'=>'dd/mm/yyyy'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Authorization Date is mandatory')
                    ])
                ]

            ])

            ->add('code_parcelle', EntityType::class, [
                'label'=>$this->translator->trans('Plot'),
                'class'=>Foret::class,
                'required'=>true,
                'multiple'=>false,
                'expanded'=>false,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'query_builder' => function (ForetRepository $parcelle): QueryBuilder {
                    return $parcelle->createQueryBuilder('f')
                        ->andWhere('f.code_type_foret = 3')
                        ->andWhere('f.code_cantonnement is not null')
                        ->orderBy('f.numero_foret', 'DESC');
                },
                'attr'=>[
                    'class'=>'form-control code_attributaire_pv alert-primary font-weight-bold'
                ]

            ])

            ->add('raisonSociale', TextType::class, [
                'label'=>$this->translator->trans('Owner'),
                'required'=>true,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control text-sm text-sm text-dark attributaire alert-primary font-weight-bold'
                ]

            ])
            ->add('mobilePersonneRessource', TextType::class, [
                'label'=>$this->translator->trans('Mobile number'),
                'required'=>true,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control text-sm text-sm text-dark mobile alert-primary font-weight-bold'
                ]

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AttributionPv::class
        ]);
    }
}