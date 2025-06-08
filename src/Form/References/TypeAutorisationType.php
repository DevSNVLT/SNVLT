<?php

namespace App\Form\References;

use App\Entity\References\GrilleLegalite;
use App\Entity\References\TypeAutorisation;
use App\Entity\References\TypeOperateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class TypeAutorisationType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, [
                'label'=>'Autorisation',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control',
                    'style'=>'background-color:lightyellow'
                ],
                'required'=>true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('The authorization cannot be blank'),
                    ])
                ]

            ])
            ->add('type_operateur', EntityType::class,[
                'class'=>TypeOperateur::class,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'choice_label'=>'libelle_operateur',
                'attr'=>[
                    'class'=>'type_autorisation_type_operateur',
                    'style'=>'background-color:lightyellow'
                ],
                'multiple'=>false,
                'expanded'=>false,
                'required'=>true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('The Operator is mandatory'),
                    ])
                ]
            ])
            ->add('code_doc_grille', EntityType::class,[
                'class'=>GrilleLegalite::class,
                'choice_label'=>'libelle_document',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'choices form-select grille multiple-remove text-dark'
                ],
                'multiple'=>true,
                'expanded'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TypeAutorisation::class,
        ]);
    }
}
