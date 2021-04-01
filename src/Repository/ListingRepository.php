<?php

namespace App\Repository;

use App\Entity\Listing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Listing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Listing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Listing[]    findAll()
 * @method Listing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Listing::class);
    }

    /**
     * @return Listing[]
     */
    public function findAllActive(): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT listing
            FROM App\Entity\Listing listing
            WHERE listing.status = 'approved'
            ORDER BY listing.id ASC"
        );

        // returns an array of Product objects
        return $query->getResult();
    }

    /**
     * @return Listing[]
     */
    public function findAllActiveWithUser($user_id): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT listing
            FROM App\Entity\Listing listing
            WHERE listing.status = :status AND listing.user_id = :user_id
            ORDER BY listing.id ASC'
        )->setParameter('status', "approved")
        ->setParameter('user_id', $user_id);

        // returns an array of Product objects
        return $query->getResult();
    }

    /**
     * @return Listing[]
     */
    public function findWithCategoryId($category_id): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT listing
            FROM App\Entity\Listing listing
            WHERE listing.category_id = " . $category_id . "
            ORDER BY listing.id ASC"
        );

        // returns an array of Product objects
        return $query->getResult();
    }

    /**
     * @return Listing[]
     */
    public function findWithCityId($city_id): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT listing
            FROM App\Entity\Listing listing
            WHERE listing.city_id = " . $city_id . "
            ORDER BY listing.id ASC"
        );

        // returns an array of Product objects
        return $query->getResult();
    }

    /**
     * @return Listing[]
     */
    public function findWithUserId($user_id): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT listing
            FROM App\Entity\Listing listing
            WHERE listing.user_id = " . $user_id . "
            ORDER BY listing.id ASC"
        );

        // returns an array of Product objects
        return $query->getResult();
    }

    /**
     * @return Listing[]
     */
    public function findWithFilter($filter): array
    {
        $query = $this->createQueryBuilder('listing');
        foreach ($filter as $key => $value)
        {
            $query->andWhere($query->expr()->andX(
                $query->expr()->like("listing.".$key, ":keyword_".$key)               
            ));
            $query->setParameter("keyword_".$key, '%'.$value.'%');
        }
        return $query->orderBy('listing.id', 'ASC')
                ->getQuery()
                ->getResult();
    }

    // /**
    //  * @return Listing[] Returns an array of Listing objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Listing
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
