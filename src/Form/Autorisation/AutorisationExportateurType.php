<?php

namespace App\Form\Autorisation;

use App\Entity\Autorisation\AgreementExportateur;
use App\Entity\Autorisation\AutorisationExportateur;
use App\Entity\References\Exportateur;
use App\Repository\Autorisation\AgreementExportateurRepository;
use App\Repository\References\ExportateurRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class AutorisationExportateurType extends AbstractType
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
                    'class'=>'form-control sigle numero_decision',
                    'style'=>'background-color:lightpink; font-size:20px; font-weight:bold;'

                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Authorization No is mandatory')
                    ])
                ]
            ])
            ->add('date_autorisation', DateType::class, [
                'label'=>$this->translator->trans('Authorization Date'),
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control date_autorisation',
                    'style'=>'background-color:lightyellow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Authorization Date is mandatory')
                    ])
                ]

            ])

            ->add('code_agreement', EntityType::class, [
                'label'=>$this->translator->trans('Exporter'),
                'class'=>AgreementExportateur::class,
                'required'=>true,
                'multiple'=>false,
                'expanded'=>false,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'query_builder' => function (AgreementExportateurRepository $att): QueryBuilder {
                    return $att->createQueryBuilder('a')
                        ->andWhere('a.reprise = false')
                        ->orderBy('a.date_decision', 'DESC');
                },
                'attr'=>[
                    'class'=>'form-control code_exportateur alert-primary font-weight-bold'
                ]

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AutorisationExportateur::class
        ]);
    }
}