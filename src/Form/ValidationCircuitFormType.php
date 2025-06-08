<?php

namespace App\Form;

use App\Entity\References\CircuitCommunication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ValidationCircuitFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('service_validation')
            ->add('num_seq')
            ->add('statut')
            ->add('type_service')
            ->add('valide')
            ->add('service_id')
            ->add('observation')
            ->add('created_at')
            ->add('created_by')
            ->add('updated_at')
            ->add('updated_by')
            ->add('operateur')
            ->add('code_modele')
            ->add('code_document_operateur')
            ->add('code_service')
            ->add('code_direction')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CircuitCommunication::class,
        ]);
    }
}
