<?php

namespace App\Repository;

use App\Entity\Businessowner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Businessowner|null find($id, $lockMode = null, $lockVersion = null)
 * @method Businessowner|null findOneBy(array $criteria, array $orderBy = null)
 * @method Businessowner[]    findAll()
 * @method Businessowner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BusinessownerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Businessowner::class);
    }

    // /**
    //  * @return Businessowner[] Returns an array of Businessowner objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Businessowner
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
