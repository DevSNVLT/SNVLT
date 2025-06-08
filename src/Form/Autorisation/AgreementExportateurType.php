<?php

namespace App\Form\Autorisation;

use App\Entity\Autorisation\AgreementExportateur;
use App\Entity\References\Exportateur;
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

class AgreementExportateurType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero_decision', TextType::class, [
                'label'=>$this->translator->trans('Decision No'),
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
                        'message' => $this->translator->trans('Decision No is mandatory')
                    ])
                ]
            ])
            ->add('date_decision', DateType::class, [
                'label'=>$this->translator->trans('Decision Date'),
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control date_decision',
                    'style'=>'background-color:lightyellow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Decision Date is mandatory')
                    ])
                ]

            ])

            ->add('code_exportateur', EntityType::class, [
                'label'=>$this->translator->trans('Exporter'),
                'class'=>Exportateur::class,
                'required'=>true,
                'multiple'=>false,
                'expanded'=>false,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'query_builder' => function (ExportateurRepository $att): QueryBuilder {
                    return $att->createQueryBuilder('e')
//                        ->andWhere('e.email_personne_ressource is not null')
//                        ->andWhere('e.statut = false or e.statut is NULL')
                        ->orderBy('e.raison_sociale_exportateur', 'ASC');
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
            'data_class' => AgreementExportateur::class
        ]);
    }
}