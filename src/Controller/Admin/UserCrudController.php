<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use App\Services\EncryptService;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;

class UserCrudController extends AbstractCrudController
{
    private $userRepository;
    public function __construct(UserRepository $userRepository){
        $this->userRepository=$userRepository;
    }
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        $id=IdField::new('id')->hideOnForm();
        $email = EmailField::new('email')
            ->formatValue(function ($value) {
                if(strpos($value, 'code-')!== false){
                    $value=null;

                }
                return $value;
            })->setCssClass('email-user');

        $serialNumber = TextField::new('serialNumber')
        ->formatValue(function ($value, $entity) {
            if(strpos($entity->getEmail(), 'code-')=== false && !empty($entity->getSerialNumber())){
                return null;

            }
            return $entity->getSerialNumber();
        })->setCssClass('serial-user')->addJsFiles('assets/scripts/js/Admin/app.js');
        $roles = ArrayField::new('roles');
        $isActive = BooleanField::new('isActive');
        $group = AssociationField::new('group');

        return [
            $id,
            $email,
            $serialNumber,
            $roles,
            $isActive,
            $group
        ];
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the names of the Doctrine entity properties where the search is made on
            // (by default it looks for in all properties)
            ->setSearchFields(['email', 'serialNumber', 'roles']);
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {

        if(!empty($searchDto->getQuery())){
            return $this->userRepository->createCustumQueryBuilder($searchDto->getQuery(), $searchDto->getSort());
        }

        if(array_key_exists('group', $searchDto->getSort())){
            return $this->userRepository->createCustumQueryBuilder($searchDto->getQuery(), $searchDto->getSort(), $searchDto->getSortDirection('group'));
        }

        return $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
    }
//    public function configureActions(Actions $actions): Actions
//    {
//        return $actions
//            // ...
//            // this will forbid to create or delete entities in the backend
//            ->disable(Action::DELETE);
//    }

}
