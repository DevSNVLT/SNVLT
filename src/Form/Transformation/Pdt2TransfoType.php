<?php

namespace App\Form\Transformation;

use App\Entity\References\TypeTransformation;
use App\Entity\Transformation\Pdt2Transfo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class Pdt2TransfoType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomPdt', TextType::class, [
                'label'=>"Produit",
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow;text-transform:uppercase;'
                ]
            ])

            ->add('typeProduit', EntityType::class, [
                'label'=>"Type de Transformation",
                'class'=>TypeTransformation::class,
                'required'=>true,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark direction',
                    'style'=>'background-color:lightyellow'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pdt2Transfo::class,
        ]);
    }
}
