<?php

namespace App\Form\Helper;

use App\Entity\Helper\Media;
use App\Entity\References\Cantonnement;
use App\Entity\References\Ddef;
use App\Entity\References\Direction;
use App\Entity\References\Dr;
use App\Entity\References\Exploitant;
use App\Entity\References\Exportateur;
use App\Entity\References\PosteForestier;
use App\Entity\References\ServiceMinef;
use App\Entity\References\TypeOperateur;
use App\Entity\References\Usine;
use App\Entity\User;
use App\Repository\References\CantonnementRepository;
use App\Repository\References\DdefRepository;
use App\Repository\References\DirectionRepository;
use App\Repository\References\ExploitantRepository;
use App\Repository\References\ExportateurRepository;
use App\Repository\References\PosteForestierRepository;
use App\Repository\References\ServiceMinefRepository;
use App\Repository\References\UsineRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class MadiatechType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fichier', FileType::class, [
                'label' => 'Charger votre media',
                'label_attr'=>[
                    'class'=>'font-weight-bold text-danger'
                ],
                'attr'=>[
                    /*'class'=>'form-control',*/
                    'style'=>'background-color:lightyellow;text-transform:uppercase;marging-left:25px;'
                ],
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                'multiple'=>false,

            ])
            ->add('actif', CheckboxType::class, [
                'label'=>$this->translator->trans('Actif ?'),
                'label_attr'=>[
                    'class'=>'font-weight-bold text-dark'
                ],
                'attr'=>[
                    /*'class'=>'form-control',*/
                    'style'=>'background-color:lightyellow;text-transform:uppercase;marging-left:25px;'
                ]
            ])
            ->add('code_operateur', EntityType::class,[
                'class'=>TypeOperateur::class,
                'placeholder' => '-- Opérateur --',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'choices form-select multiple-remove',
                    'style'=>'background-color:lightyellow'
                ],

                'multiple'=>true,
                'expanded'=>false,
                'required'=>true
            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}