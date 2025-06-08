<?php

namespace App\Controller\Admin;

use App\Entity\Autres\Contacter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ContacterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contacter::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return  $actions
            ->remove(Crud::PAGE_INDEX, actionName: \EasyCorp\Bundle\EasyAdminBundle\Config\Action::NEW);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nom', 'Nom');
        yield TextField::new('email', 'Email');
        yield TextField::new('contact', 'Contact');
        yield TextField::new('subject', 'Sujet');
        yield TextField::new('message', 'Message');
    }
}
