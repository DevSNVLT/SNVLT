<?php

namespace App\Form\Administration;


use App\Entity\Administration\Gadget;
use App\Entity\Groupe;
use App\Entity\References\TypeOperateur;
use App\Repository\Autorisations\AttributionRepository;
use App\Repository\GroupeRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class GadgetType extends  AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, [
                'label'=>$this->translator->trans('Widget name'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow'
                ]
            ])
            ->add('reference', TextType::class, [
                'label'=>$this->translator->trans('Widget Reference'),
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow'
                ]
            ])

            ->add('code_groupe', EntityType::class, [
                'label'=>$this->translator->trans('Operator'),
                'class'=>Groupe::class,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'choices form-select multiple-remove',
                    'style'=>'background-color:lightyellow'
                ],
                'multiple'=>true,
                'expanded'=>false,
                'required'=>false,
                'query_builder' => function (GroupeRepository $gr): QueryBuilder {
                    return $gr->createQueryBuilder('g')
                        ->andWhere('g.groupe_system = true')
                        ->andWhere('g.id > 1');
                }
            ]) ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Gadget::class,
        ]);
    }
}