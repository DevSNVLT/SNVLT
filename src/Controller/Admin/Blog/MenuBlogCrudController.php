<?php

namespace App\Controller\Admin\Blog;

use App\Entity\Blog\MenuBlog;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class MenuBlogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MenuBlog::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
