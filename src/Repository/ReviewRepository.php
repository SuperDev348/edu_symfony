<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * @return Review[]
     */
    public function findAllWithListingId(int $id): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT review
            FROM App\Entity\Review review
            WHERE review.listing_id = :id
            ORDER BY review.date ASC'
        )->setParameter('id', $id);

        // returns an array of Product objects
        return $query->getResult();
    }

    /**
     * @return Review[]
     */
    public function findWithFilter($filter): array
    {
        $query = $this->createQueryBuilder('review');
        foreach ($filter as $key => $value)
        {
            $query->andWhere($query->expr()->andX(
                $query->expr()->like("review.".$key, ":keyword_".$key)               
            ));
            $query->setParameter("keyword_".$key, '%'.$value.'%');
        }
        return $query->orderBy('review.id', 'ASC')
                ->getQuery()
                ->getResult();
    }

    /**
     * @return Review[]
     */
    public function findLatest($listings, int $num = 5): array
    {
        if (count($listings) == 0)
            return [];
        $query = $this->createQueryBuilder('review');
        foreach ($listings as $key => $listing)
        {
            $query->orWhere($query->expr()->orX(
                $query->expr()->like("review.listing_id", ":keyword_".$key)               
            ));
            $query->setParameter("keyword_".$key, '%'.$listing->getId().'%');
        }

        return $query->orderBy('review.id', 'DESC')
            ->setMaxResults($num)
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Review[] Returns an array of Review objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Review
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
