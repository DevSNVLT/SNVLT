<?php

namespace App\Controller\Admin\Blog;

use App\Entity\Blog\ArticleBlog;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ArticleBlogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ArticleBlog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Articles')
            ->setPageTitle(Action::NEW , 'Ajouter un nouvel article');
    }

    public function configureFields(string $pageName): iterable
    {
            yield TextField:: new('title');
            yield SlugField:: new('slug')->setTargetFieldName('title');
            yield TextEditorField:: new('content');
            yield TextField:: new('featuredText');
            yield AssociationField:: new('categories');
            yield DateField:: new('createdAt')->hideOnForm();
            yield DateField:: new('updateAt')->hideOnForm();
            yield AssociationField::new('featuredImage');
    }

}
