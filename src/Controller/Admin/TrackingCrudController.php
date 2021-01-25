<?php

namespace App\Controller\Admin;


use App\Entity\Tracking;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

class TrackingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tracking::class;
    }

    public function configureFields(string $pageName): iterable
    {

        return [
            TextField::new('login'),
            AssociationField::new('user')->formatValue(function ($value, $entity) {
                return $entity->getUser() ? $entity->getUser()->getId() : null;
            })->hideOnForm()->setSortable(false),
            DateTimeField::new('lastLogin')->formatValue(function ($value, $entity) {
                return $entity->getLastLogin() ? $entity->getLastLogin()->format('Y-m-d H:i:s'): null;
            })
        ];
    }
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('login')
//            ->add('user')
            ->add('lastLogin')
            ;
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
            // this will forbid to create or delete entities in the backend
            ->disable(Action::EDIT, Action::DELETE, Action::NEW)
            ;
    }

}
