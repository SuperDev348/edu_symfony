<?php

namespace App\Repository;

use App\Entity\Blogtype;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Blogtype|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blogtype|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blogtype[]    findAll()
 * @method Blogtype[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogtypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blogtype::class);
    }

    // /**
    //  * @return Blogtype[] Returns an array of Blogtype objects
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
    public function findOneBySomeField($value): ?Blogtype
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
