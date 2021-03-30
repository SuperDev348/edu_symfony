<?php

namespace App\Repository;

use App\Entity\BlogCommentLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlogCommentLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogCommentLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogCommentLike[]    findAll()
 * @method BlogCommentLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogCommentLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogCommentLike::class);
    }

    /**
     * @return BlogComment[]
     */
    public function findWithUser(int $id): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT blog_comment_like
            FROM App\Entity\BlogCommentLike blog_comment_like
            WHERE blog_comment_like.user_id = :id'
        )->setParameter('id', $id);

        // returns an array of Product objects
        return $query->getResult();
    }

    // /**
    //  * @return BlogCommentLike[] Returns an array of BlogCommentLike objects
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
    public function findOneBySomeField($value): ?BlogCommentLike
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
