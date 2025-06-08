<?php

namespace App\Form\Administration;

use App\Entity\Groupe;
use App\Entity\Permission;
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
use App\Repository\PermissionRepository;
use App\Repository\References\CantonnementRepository;
use App\Repository\References\DdefRepository;
use App\Repository\References\DirectionRepository;
use App\Repository\References\DrRepository;
use App\Repository\References\ExploitantRepository;
use App\Repository\References\ExportateurRepository;
use App\Repository\References\PosteForestierRepository;
use App\Repository\References\ServiceMinefRepository;
use App\Repository\References\UsineRepository;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;


class MyGroupsType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('nom_groupe', TextType::class, [
                'label'=>'Dénomination du groupe',
                'label_attr'=>[
                    'class'=>'font-weight-bold'
                ],
                'attr' => [
                    'class'=>'form-control ps-0 form-control-line uppercase',
                    'style'=>'text-transform:uppercase;background:lightyellow;'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Please Group name is mandatory'),
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => $this->translator->trans('The group name must contain at least '). '{{ limit }}'. $this->translator->trans(' characters'),
                        // max length allowed by Symfony for security reasons
                        'max' => 255,
                    ]),
                ]
            ])


        ;



    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Groupe::class,
        ]);
    }
}
