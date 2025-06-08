<?php

namespace App\Controller\Admin;

use App\Entity\Blog\GroupePublication;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GroupePublicationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GroupePublication::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Groupes de publication')
            ->setPageTitle(Action::NEW , 'Ajouter un nouveau groupe');
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField:: new('libelle', 'Libellé de la catégorie');
    }
}
