<?php

namespace App\Controller\Admin\Securite;


use App\Entity\Admin\Option;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Collection\EntityCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

class OptionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Option::class;
    }

    public function index(AdminContext $context)
    {
        $response = parent::index($context);

        if ($response instanceof  Response){
            return $response;
        };
        /** @var EntityCollection */
        $entities = $response->get('entities');

        foreach ($entities as $entity){
            $Fields = $entity->getFields();
            $Fields->unset($Fields->getByProperty('type'));
        }
        return $response;
    }

    public function createEditForm(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormInterface
    {
        $formBuilder = parent::createEditForm($entityDto, $formOptions, $context);
        /** @var  Option */
        $viewData = $formBuilder->getViewData();
        $value = $viewData->getValue();

        $formBuilder->add('value', $viewData->getType(), [
            'data'=>$viewData->getType() == CheckboxType::class ? boolval($value) : $value,
            'label'=>'Valeur',
            'required'=>false
        ]);
        return $formBuilder;
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var  $option */
        $option = $entityInstance;

        if ($option->getType() === CheckboxType::class){
            $value = $option->getValue() ? '1' : '0';
            $option->setValue($value);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::BATCH_DELETE)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::NEW);
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            /*->setEntityPermission('ROLE_USER')*/
            /*->setSearchFields(null)*/
            ->setEntityLabelInPlural('Options SNVLT')
            ->showEntityActionsInlined();
    }

    public function configureFields(string $pageName): iterable
    {
       yield TextField::new('label','Option')
           ->setFormTypeOption('attr',[
               'readOnly'=>true
           ]);
        yield TextField::new('categorie','Catégorie');

        yield TextField::new('value','valeur');

        yield HiddenField::new('type');


    }

}
