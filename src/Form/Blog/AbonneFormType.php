<?php

namespace App\Form\Blog;

use App\Entity\Blog\Abonne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbonneFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenoms')
            ->add('telephone')
            ->add('email')
            ->add('newsletter')
            ->add('pseudo')
            ->add('avatar')
            ->add('created_at')
            ->add('updated_at')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Abonne::class,
        ]);
    }
}
