<?php

namespace App\Form\Observateur;

use App\Entity\Observateur\AnalyseRapport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnalyseRapportAdminType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('libelle', TextType::class, [
                'label'=>'Sujet',
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow; font-weight:bold'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Renseignez SVP le sujet')
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => $this->translator->trans('le libellé ou Sujet doiot avoir au moins '). '{{ limit }}'.$this->translator->trans(' characters'),
                        // max length allowed by Symfony for security reasons
                        'max' => 255,
                    ]),
                ]
            ])

            ->add('fichier', FileType::class, [
                'label' => 'Charger votre fichier',

                'mapped' => false,

                'required' => true,

                'constraints' => [
                    new File([
                        'maxSize' => '15360k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                        ],
                        'maxSizeMessage'=> "SVP, Chargez un fichier de moins de  15 Mb",
                        'mimeTypesMessage' =>"SVP, Chargez un fichier valide"
                    ])

                ],
            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AnalyseRapport::class,
        ]);
    }
}