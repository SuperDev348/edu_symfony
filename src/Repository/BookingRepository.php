<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * @return Booking[]
     */
    public function findLatest($listings, int $num = 5): array
    {
        if (count($listings) == 0)
            return [];
        $query = $this->createQueryBuilder('booking');
        foreach ($listings as $key => $listing)
        {
            $query->orWhere($query->expr()->orX(
                $query->expr()->like("booking.listing_id", ":keyword_".$key)               
            ));
            $query->setParameter("keyword_".$key, '%'.$listing->getId().'%');
        }

        return $query->orderBy('booking.id', 'DESC')
            ->setMaxResults($num)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Booking[]
     */
    public function findWithListingId($listing_id): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT booking
            FROM App\Entity\Booking booking
            WHERE booking.listing_id = " . $listing_id . "
            ORDER BY booking.id ASC"
        );

        // returns an array of Product objects
        return $query->getResult();
    }

    /**
     * @return Booking[]
     */
    public function findWithFilter($filter): array
    {
        $query = $this->createQueryBuilder('booking');
        foreach ($filter as $key => $value)
        {
            $query->andWhere($query->expr()->andX(
                $query->expr()->like("booking.".$key, ":keyword_".$key)               
            ));
            $query->setParameter("keyword_".$key, '%'.$value.'%');
        }
        return $query->orderBy('booking.id', 'ASC')
                ->getQuery()
                ->getResult();
    }

    // /**
    //  * @return Booking[] Returns an array of Booking objects
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
    public function findOneBySomeField($value): ?Booking
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
