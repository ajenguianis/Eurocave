<?php

namespace App\Repository;

use App\Entity\Import;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Import|null find($id, $lockMode = null, $lockVersion = null)
 * @method Import|null findOneBy(array $criteria, array $orderBy = null)
 * @method Import[]    findAll()
 * @method Import[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Import::class);
    }

    /**
     * @param $fileName
     * @return Import
     */
    public function create($fileName){
        $import= new Import();
        $import->setFileName($fileName);
        $import->setStatus('PENDING');
        $import->setImportedAt(new \DateTime());
        $this->save($import);
        return $import;
    }

    /**
     * @param Import $import
     * @param $status
     * @return Import
     */
    public function update(Import $import, $status){
        $import->setStatus($status);
        $this->save($import);
        return $import;

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
