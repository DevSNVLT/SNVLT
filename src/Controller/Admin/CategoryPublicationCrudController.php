<?php

namespace App\Controller\Admin;

use App\Entity\Blog\CategoryPublication;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryPublicationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CategoryPublication::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Catégories de Publication')
            ->setPageTitle(Action::NEW , 'Ajouter une nouvelle catégorie');
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField:: new('libelle', 'Dénomination de la catégorie');
        yield SlugField:: new('slug')->setTargetFieldName('libelle');
        yield AssociationField:: new('code_groupe', 'Sélectionnez le groupe');
    }
}
