<?php

namespace App\Repository;

use App\Entity\Tracking;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method Tracking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tracking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tracking[]    findAll()
 * @method Tracking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackingRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tracking::class);
    }

    /**
     * @param User $user
     * @return Tracking
     */
    public function createTracking(User $user){
        $date=new \DateTime();
        $tracking= new Tracking();
        $tracking->setLastLogin($date);
        $tracking->setUser($user);
        if(!$user->getSerialNumber()){
            $tracking->setLogin($user->getEmail());
        }else{
            $tracking->setLogin($user->getSerialNumber());
        }
        $this->save($tracking);
        return $tracking;
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

}
