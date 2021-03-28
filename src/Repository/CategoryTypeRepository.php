<?php

namespace App\Repository;

use App\Entity\CategoryType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CategoryType|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryType|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryType[]    findAll()
 * @method CategoryType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryType::class);
    }

    // /**
    //  * @return CategoryType[] Returns an array of CategoryType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CategoryType
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
