<?php

namespace App\Repository;

use App\Entity\User;
use App\Services\EncryptService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }
    /**
     * @param object $entity
     */
    public function save($entity)
    {
        $this->_em->persist($entity);
        $this->_em->flush();
    }

    /**
     * @param $query
     * @param array $sort
     * @param string $sortDirection
     * @return QueryBuilder
     */
    public function createCustumQueryBuilder($query,array $sort, $sortDirection='DESC'): QueryBuilder
    {
        $entityManager =$this->_em;

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $entityManager->createQueryBuilder()
            ->select('entity')
            ->from(User::class, 'entity')
            ->join('entity.group', 'g')
        ;

        if (!empty($query)) {
            $pos = strpos($query, '@');

            if ($pos !== false) {
                $queryBuilder->orWhere(sprintf('%s.%s = :query_for_email', 'entity', 'email'))
                    ->setParameter('query_for_email', $query);
            }
            $queryBuilder->orWhere(sprintf('%s.%s = :query_for_numbers', 'entity', 'serialNumber'))
                ->setParameter('query_for_numbers', EncryptService::encodeData($query));

            $queryBuilder->orWhere(sprintf('%s.%s like :query_for_roles', 'entity', 'roles'))
                ->setParameter('query_for_roles', '%' . $query . '%');

            $queryBuilder->orWhere('g.name = :name');
            $queryBuilder->setParameter('name', $query);
        }
        $queryBuilder->orderBy('entity.id', $sortDirection);

        if(array_key_exists('group', $sort)){
            $queryBuilder->orderBy('g.name', $sortDirection);
        }


        return $queryBuilder;
    }
    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
