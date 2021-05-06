<?php

namespace Akyos\PaymentBundle\Repository;

use Akyos\PaymentBundle\Entity\PaymentOptions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PaymentOptions|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentOptions|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentOptions[]    findAll()
 * @method PaymentOptions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentOptionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentOptions::class);
    }

    // /**
    //  * @return PaymentOptions[] Returns an array of PaymentOptions objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PaymentOptions
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
