<?php

namespace App\Controller\Admin\Blog;

use App\Entity\Blog\Categorie;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategorieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Categorie::class;
    }


    public function configureFields(string $pageName): iterable
    {
        yield TextField:: new('name');
        yield SlugField:: new('slug')->setTargetFieldName('name');
        yield ColorField:: new('color');
    }

}
