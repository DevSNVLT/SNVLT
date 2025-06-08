<?php

namespace App\Form\DocStats\Pages;

use App\Entity\DocStats\Entetes\Documentbcbp;
use App\Entity\DocStats\Pages\Pagebcbp;
use App\Entity\References\Cantonnement;
use App\Entity\References\PageDocGen;
use App\Entity\References\Usine;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Contracts\Translation\TranslatorInterface;

class PagebcbpType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('photo', FileType::class, [
                'label' =>false,  // $this->translator->trans('Upload a CSV file'),

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                'attr'=>[
                    'class'=>'monfichier',
                    'style'=>'
                                width: 350px;
                                max-width: 100%;
                                color: #444;
                                padding: 5px;
                                background: #fff;
                                border-radius: 10px;
                                border: 1px solid #555;'
                ],
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '3072k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                        ],
                        'maxSizeMessage'=> $this->translator->trans('Please, upload an image file with size less than 3 Mb'),
                        'mimeTypesMessage' => $this->translator->trans('Please, upload a valid image')
                    ])

                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pagebcbp::class,
        ]);
    }
}
