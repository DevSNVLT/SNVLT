<?php

namespace App\Form\Observateur;

use App\Entity\Observateur\AnalyseRapport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnalyseRapportType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('fichier', FileType::class, [
                'label' => 'Charger votre fichier',

                'mapped' => false,

                'required' => true,

                'constraints' => [
                    new NotBlank([
                        'message' => "Merci de charger un fichier"
                    ]),
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