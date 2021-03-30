<?php

namespace App\Repository;

use App\Entity\BlogComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlogComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogComment[]    findAll()
 * @method BlogComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogComment::class);
    }

    /**
     * @return BlogComment[]
     */
    public function findAllWithBlogId(int $id): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT blog_comment
            FROM App\Entity\BlogComment blog_comment
            WHERE blog_comment.blog_id = :id
            ORDER BY blog_comment.date ASC'
        )->setParameter('id', $id);

        // returns an array of Product objects
        return $query->getResult();
    }

    /**
     * @return BlogComment[]
     */
    public function findWithBlogId(int $blog_id, int $reply_id): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT blog_comment
            FROM App\Entity\BlogComment blog_comment
            WHERE blog_comment.blog_id = :blog_id AND blog_comment.reply_id = :reply_id
            ORDER BY blog_comment.date ASC'
        )
        ->setParameter('blog_id', $blog_id)
        ->setParameter('reply_id', $reply_id);

        // returns an array of Product objects
        return $query->getResult();
    }

    // /**
    //  * @return BlogComment[] Returns an array of BlogComment objects
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
    public function findOneBySomeField($value): ?BlogComment
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
