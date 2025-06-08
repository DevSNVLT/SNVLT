<?php

namespace App\Form\References;

use App\Entity\References\Essence;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\BooleanFilterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EssencesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero_essence', TextType::class, [
                'label'=>'Code essence',
                'label_attr'=>[
                    'class'=>'font-weight-bold text-dark'
                ],
                'attr'=>[
                    'maxlength' => 4,
                    'style'=>'font-size:16px;text-transform:uppercase;background-color:lightyellow;'
                ],
                'required'=>true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le code essence est obligatoire',
                    ])
                ]
            ])
            ->add('nom_vernaculaire', TextType::class, [
                'label'=>'Nom vernaculaire',
                'label_attr'=>[
                    'class'=>'font-weight-bold text-dark'
                ],
                'attr'=>[
                    'style'=>'font-size:16px;text-transform:uppercase;background-color:lightyellow;'
                ],
                'required'=>true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom vernaculaire est obligatoire',
                    ])
                ]
            ])
            ->add('famille_essence', TextType::class, [
                'label'=>'Famille Essence',
                'label_attr'=>[
                    'class'=>'font-weight-bold text-dark'
                ],
                'attr'=>[
                    'style'=>'font-size:16px;text-transform:uppercase;background-color:lightyellow;'
                ],
            ])
            ->add('nom_scientifique', TextType::class, [
                'label'=>'Nom scientifique',
                'label_attr'=>[
                    'class'=>'font-weight-bold text-dark'
                ],
                'attr'=>[
                    'style'=>'font-size:16px;text-transform:uppercase;background-color:lightyellow;'
                ],
            ])
            ->add('categorie_essence', TextType::class, [
                'label'=>'Catégorie',
                'label_attr'=>[
                    'class'=>'font-weight-bold text-dark'
                ],
                'attr'=>[
                    'style'=>'font-size:16px;text-transform:uppercase;background-color:lightyellow;'
                ]
            ])
            ->add('taxe_abattage', NumberType::class, [
                'label'=>'Taxe abattage',
                'label_attr'=>[
                    'class'=>'font-weight-bold text-dark'
                ],
                'attr'=>[
                    'style'=>'font-size:16px;text-transform:uppercase;background-color:lightyellow;'
                ]
            ])
            ->add('dm_minima', NumberType::class, [
                'label'=>'Diamètre Minima',
                'label_attr'=>[
                    'class'=>'font-weight-bold text-danger'
                ],
                'attr'=>[
                    'style'=>'font-size:16px;text-transform:uppercase;background-color:lightyellow;'
                ],
                'required'=>true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le diamètre Minima est obligatoire',
                    ])
                ]
            ])
            ->add('taxe_preservation', NumberType::class, [
                'label'=>'Taxe Préservation',
                'label_attr'=>[
                    'class'=>'font-weight-bold text-dark'
                ],
                'attr'=>[
                    'style'=>'font-size:16px;text-transform:uppercase;background-color:lightyellow;'
                ]
            ])
            ->add('autorisation', CheckboxType::class, [
                'label'=>'Cette essence est-elle autorisée à la coupe ? cochez la case si OUI',
                'label_attr'=>[
                    'class'=>'font-weight-bold text-danger'
                ],
                'attr'=>[
                    'style'=>'font-size:16px;text-transform:uppercase;background-color:lightyellow;'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Essence::class,
        ]);
    }
}
