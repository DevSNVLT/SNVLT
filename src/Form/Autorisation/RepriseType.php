<?php

namespace App\Form\Autorisation;

use App\Entity\Admin\Exercice;
use App\Entity\Autorisation\Attribution;
use App\Entity\Autorisation\Reprise;
use App\Entity\References\Exploitant;
use App\Repository\Autorisations\AttributionRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class RepriseType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator, private ManagerRegistry $registry, private RequestStack $requestStack)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('numeroAutorisation', TextType::class, [
                'label'=>'N° Décision',
                'required'=>true,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Authorization number is mandatory')
                    ])
                ]
            ])
            ->add('dateAutorisation', DateType::class, [
                'label'=>'Date Décision',
                'widget'=>'single_text',
                'required'=>true,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control sigle',
                    'style'=>'background-color:lightyellow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Authorization date is mandatory')
                    ])
                ]

            ])

            ->add('codeAttribution', EntityType::class, [
                'label'=>'Forêt',
                'class'=>Attribution::class,
                'required'=>true,
                'multiple'=>false,
                'expanded'=>false,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control code_attribution'
                ],
                'query_builder' => function (AttributionRepository $attributio,): QueryBuilder {

                    $exercice = $this->registry->getRepository(Exercice::class)->find($this->requestStack->getSession()->get("exercice"));
                    //dd($exercice);
                    return $attributio->createQueryBuilder('a')
                        ->andWhere('a.statut = true')
                        ->andWhere('a.reprise = false')
                        ->andWhere('a.exercice = :val')
                        ->setParameter('val', $exercice);
                }

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reprise::class,
        ]);
    }
}
