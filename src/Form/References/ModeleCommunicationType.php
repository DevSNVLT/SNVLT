<?php

namespace App\Form\References;

use App\Entity\References\ModeleCommunication;
use App\Entity\References\TypeModeleCommunication;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ModeleCommunicationType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           /* ->add('id', HiddenType::class, [
                'attr'=>[
                    'id'=>'id_modele'
                ],
                ])*/
            ->add('libelle_modele', TextType::class, [
                'label'=>$this->translator->trans('Model Name'),
                'required'=>true,
                'label_attr'=>[
                    'class'=>'text-danger fw-bold',
                    'style'=>'font-size:16px; font-weight:bold;'
                ],
                'attr'=>[
                    'class'=>'form-control modeletext',
                    'style'=>'background:lightyellow'
                ],
                'constraints'=>[
                    new NotBlank([
                        'message' => $this->translator->trans('The model name cannot be null'),
                    ])
                ]
            ])
            ->add('code_type_modele_communication', EntityType::class, [
                'label'=>$this->translator->trans('Model Type Name'),
                'class'=>TypeModeleCommunication::class,
                'required'=>true,
                'label_attr'=>[
                    'class'=>'text-danger fw-bold',
                    'style'=>'font-size:16px; font-weight:bold;'
                ],
                'attr'=>[
                    'class'=>'form-control typemodele',
                    'style'=>'background:lightyellow'
                ],
                'multiple'=>false,
                'expanded'=>false,
                'constraints'=>[
                    new NotBlank([
                        'message' => $this->translator->trans('The model name cannot be null'),
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ModeleCommunication::class,
        ]);
    }
}
