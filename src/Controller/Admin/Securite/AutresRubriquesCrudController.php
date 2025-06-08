<?php

namespace App\Controller\Admin\Securite;

use App\Entity\Blog\AutresRubriques;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AutresRubriquesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AutresRubriques::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Informations additionnelles')
            ->setPageTitle(Action::NEW , 'Mettre à jour les informations du ministre');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::NEW);
    }

    public function configureFields(string $pageName): iterable
    {
       $mediaDir = $this->getParameter('medias_directory');
       $uploadDir = $this->getParameter('uploads_directory');

        yield TextField::new('nom_prenoms', 'Nom et Prénoms du ministre');
       yield TextEditorField::new('mot_ministre', 'Le mot du ministre')->hideOnIndex();



       $imageField = ImageField::new('photo_ministre', 'Insérer une photo du ministre')
           ->setBasePath($uploadDir)
           ->setUploadDir($mediaDir)
           ->setUploadedFileNamePattern('[slug]-[uuid].[extension]');
       if(Crud::PAGE_EDIT == $pageName){
           $imageField->setRequired(false);
       }
       yield $imageField;
    }

}
