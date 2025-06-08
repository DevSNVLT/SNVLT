<?php

namespace App\Form\Admin\Coupon;

use App\Entity\Admin\Coupon;
use App\Entity\References\Direction;
use App\Entity\Transformation\Contrat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class CouponType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codeContrat', EntityType::class, [
                'label'=>$this->translator->trans('Client'),
                'class'=>Contrat::class,
                'choice_label'=>'raison_sociale_clt',
                'multiple'=>false,
                'expanded'=>false,
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
                        'message' =>  $this->translator->trans('The direction name is mandatory'),
                    ])
                ]
            ])

            /*->add('codeCoupon', TextType::class, [
                'label'=>$this->translator->trans('Code Coupon'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:yellow;font-size:24px;font-weight:bold;width:35%',
                    'readOnly'=>true
                ],
                'constraints' => [
                    new NotBlank([
                        'message' =>  $this->translator->trans('Le code Coupon est obligatoire'),
                    ])
                ]
            ])*/

            ->add('nbJours', NumberType::class, [
                'label'=>$this->translator->trans('Validité (jours)'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],

                'attr'=>[
                    'class'=>'form-control',
                    'style'=>'background-color:lightblue'
                ]

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Coupon::class,
        ]);
    }
}