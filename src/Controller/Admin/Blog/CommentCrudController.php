<?php

namespace App\Controller\Admin\Blog;

use App\Entity\Blog\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return  $actions
            ->remove(Crud::PAGE_INDEX, actionName: \EasyCorp\Bundle\EasyAdminBundle\Config\Action::NEW);
    }

    public function configureFields(string $pageName): iterable
    {
       yield TextField::new('content');
       yield DateTimeField::new('createdAt');
       yield AssociationField::new('utilisateur');
    }

}
