<?php

namespace App\Form\Autorisation;

use App\Entity\Autorisation\Attribution;
use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Repository\References\ExploitantRepository;
use App\Repository\References\ForetRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class AttributionType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero_decision', IntegerType::class, [
                'label'=>'N° Décision',
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
                        'message' => $this->translator->trans('Decision number is mandatory')
                    ])
                ]
            ])
            ->add('date_decision', DateType::class, [
                'label'=>'Date Décision',
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
                        'message' => $this->translator->trans('Decision date is mandatory')
                    ])
                ]

            ])

            ->add('code_foret', EntityType::class, [
                'label'=>'Forêt',
                'class'=>Foret::class,
                'required'=>true,
                'multiple'=>false,
                'expanded'=>false,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control code_foret'
                ],
                'query_builder' => function (ForetRepository $foret): QueryBuilder {
                    return $foret->createQueryBuilder('f')
                        ->andWhere('f.attribue =false')
                        ->andWhere('f.code_cantonnement is not null')
                        ->andWhere('f.code_type_foret = 1');
                }

            ])

            ->add('code_exploitant', EntityType::class, [
                'label'=>'Exploitant forestier',
                'class'=>Exploitant::class,
                'required'=>true,
                'multiple'=>false,
                'expanded'=>false,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'query_builder' => function (ExploitantRepository $exploitant): QueryBuilder {
                    return $exploitant->createQueryBuilder('e')
                        ->andWhere('e.email_personne_ressource is not null')
                        ->orderBy('e.raison_sociale_exploitant', 'ASC');
                },
                'attr'=>[
                    '           class'=>'form-control text-sm text-sm alert-light text-dark code_exploitant'
                ]

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Attribution::class,
        ]);
    }
}
