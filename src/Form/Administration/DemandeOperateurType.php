<?php

namespace App\Form\Administration;

use App\Entity\Administration\DemandeOperateur;
use App\Entity\References\TypeDocumentStatistique;
use App\Repository\References\TypeDocumentStatistiqueRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class DemandeOperateurType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('doc_stat', EntityType::class, [
                'label'=>$this->translator->trans('Statistic document'),
                'class'=>TypeDocumentStatistique::class ,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
        '           class'=>'form-control alert-light text-dark fs-2 codetype'
                ],
                'query_builder' => function (TypeDocumentStatistiqueRepository $tds): QueryBuilder {
                    return $tds->createQueryBuilder('t')
                        ->andWhere('t.code_type_operateur = 2');
                },
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('The document is mandatory'),
                    ])
                ]
            ])

            ->add('qte', NumberType::class, [
                'label'=>'Quantité',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark fs-2'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark fs-2 text-center text-danger'
                ],

                'constraints' => [
                    new NotBlank([
                        'message' =>  $this->translator->trans('Quantity is mandatory'),
                    ]),
                    new Length([
                        'max'=>200,
                        'maxMessage'=> $this->translator->trans('The maximum number of documents to generate is {{ limit }}'),
                        'min'=>1,
                        'minMessage'=> $this->translator->trans('The minimum number of documents to generate is {{ limit }}'),
                    ]),
                ]

            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DemandeOperateur::class,
        ]);
    }
}
