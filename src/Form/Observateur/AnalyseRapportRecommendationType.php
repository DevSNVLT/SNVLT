<?php

namespace App\Form\Observateur;

use App\Entity\Observateur\AnalyseRapport;
use App\Entity\Observateur\StatutRapportOI;
use App\Entity\References\TypeOperateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnalyseRapportRecommendationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('numeroLigne', TextType::class, [
                'label'=>'N° de la recommendation',
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control text-center text-white font-weight-bold',
                    'style'=>'background: lightcoral;font-size: 20px;width: 100%;',
                    'readOnly'=>true,
                    'id'=>'numero_ligne'
                ]
            ])


            ->add('statut', EntityType::class,[
                'label'=>false,
                'class'=>StatutRapportOI::class,
                'placeholder' => 'Statut',
                'attr'=>[
                    'class'=>'form-control'
                ],

                'multiple' => false,
                'expanded' => false,
                'required'=>true
            ])
            ->add('fichierRecommande', FileType::class, [
                'label' => 'Charger votre fichier',

                'mapped' => false,

                /*'required' => true,*/

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
                    /*new NotBlank([
                        'message' => 'Merci de charger un fichier avant de continuer',
                    ])*/

                ]
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