<?php

namespace App\Form\Administration;

use App\Entity\Administration\StockDoc;
use App\Entity\References\Imprimeur;
use App\Entity\References\TypeDocumentStatistique;
use App\Repository\References\ExploitantRepository;
use App\Repository\References\TypeDocumentStatistiqueRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class StockDocType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codeTypeDocStat', EntityType::class, [
                'label'=>'Document',
                'class'=>TypeDocumentStatistique::class ,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
        '           class'=>'form-control alert-light text-dark fs-2 codetype'
                ],

                'constraints' => [
                    new NotBlank([
                        'message' => 'Le document est obligatoire',
                    ])
                ],
                'query_builder' => function (TypeDocumentStatistiqueRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('t')
                        ->where("t.statut = 'ACTIF'")
                        ->orderBy('t.abv', 'ASC');
                }
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
                        'message' => 'La quantité est obligatoire',
                    ]),
                    new Length([
                        'max'=>200,
                        'maxMessage'=>'Le maximum de documents à générer est {{ limit }}',
                        'min'=>1,
                        'minMessage'=>'Le minimum de documents à générer est {{ limit }}',
                    ]),
                ]

            ])

            ->add('code_imprimeur', EntityType::class, [
                'class'=>Imprimeur::class,
                'label'=>'Imprimeur',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark fs-2'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark fs-2 text-center text-danger'
                ],
                'multiple'=>false,
                'required'=>true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Sélectionnez l`imprimeur SVP',
                    ])
                ]

            ])

            ->add('lettre', ChoiceType::class, [
                'label'=>'Série',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark fs-2'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark fs-2 text-center text-danger'
                ],
                'choices'=>[
                    'A'=>'A',
                    'B'=>'B',
                    'C'=>'C',
                    'D'=>'D',
                    'E'=>'E',
                    'F'=>'F',
                    'G'=>'G',
                    'H'=>'H',
                    'I'=>'I',
                    'J'=>'J',
                    'K'=>'K',
                    'L'=>'L',
                    'M'=>'M',
                    'O'=>'O',
                    'P'=>'P',
                    'Q'=>'Q',
                    'R'=>'R',
                    'S'=>'S',
                    'T'=>'T',
                    'U'=>'U',
                    'V'=>'V',
                    'W'=>'W',
                    'X'=>'X',
                    'Y'=>'Y',
                    'Z'=>'Z'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'La série de document est obligatoire',
                    ])
                ]

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StockDoc::class,
        ]);
    }
}
