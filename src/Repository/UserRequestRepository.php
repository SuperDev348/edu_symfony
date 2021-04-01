<?php

namespace App\Repository;

use App\Entity\UserRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserRequest[]    findAll()
 * @method UserRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserRequest::class);
    }

    /**
     * @return UserRequest[]
     */
    public function findLatest(int $num = 5): array
    {
        $entityManager = $this->getEntityManager();

        return $this->createQueryBuilder('userrequest')
            ->orderBy('userrequest.id', 'DESC')
            ->setMaxResults($num)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return UserRequest[]
     */
    public function findWithListingId($listing_id): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT user_request
            FROM App\Entity\UserRequest user_request
            WHERE user_request.listing_id = " . $listing_id . "
            ORDER BY user_request.id ASC"
        );

        // returns an array of Product objects
        return $query->getResult();
    }

    /**
     * @return UserRequest[]
     */
    public function findWithFilter($filter): array
    {
        $query = $this->createQueryBuilder('user_request');
        foreach ($filter as $key => $value)
        {
            $query->andWhere($query->expr()->andX(
                $query->expr()->like("user_request.".$key, ":keyword_".$key)               
            ));
            $query->setParameter("keyword_".$key, '%'.$value.'%');
        }
        return $query->orderBy('user_request.id', 'ASC')
                ->getQuery()
                ->getResult();
    }

    // /**
    //  * @return UserRequest[] Returns an array of UserRequest objects
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
    public function findOneBySomeField($value): ?UserRequest
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
