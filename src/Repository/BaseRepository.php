<?php

namespace App\Repository;

use App\Entity\Import;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class BaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Import::class);
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
     * @param object $entity
     */
    public function delete($entity)
    {
        $this->_em->remove($entity);
        $this->_em->flush();
    }
    // /**
    //  * @return Import[] Returns an array of Import objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Import
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
