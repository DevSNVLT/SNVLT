<?php

namespace App\Controller\Admin;

use App\Entity\Blog\Publication;
use App\Form\Blog\PublicationFichiersType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use mysql_xdevapi\CollectionFind;

class PublicationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Publication::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Publication des textes')
            ->setPageTitle(Action::NEW , 'Ajouter une nouvelle publication');
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField:: new('libelle_publication', 'Titre');
        yield AssociationField:: new('code_category');
        yield CollectionField:: new('fichierPublications')
            ->setEntryType(PublicationFichiersType::class);

        $mediaDir = $this->getParameter('medias_directory');
        $uploadDir = $this->getParameter('uploads_directory');

        $imageField = ImageField::new('fichier', 'Fichier')
            ->setBasePath($uploadDir)
            ->setUploadDir($mediaDir)
            ->setUploadedFileNamePattern('[uuid].[extension]');
        if(Crud::PAGE_EDIT == $pageName){
            $imageField->setRequired(false);
        }
        yield $imageField;
    }
}
