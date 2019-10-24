<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findAllPlusComments()
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.comments', 'c')
            ->addSelect('c')
            ->getQuery()
            ->getResult();
    }

    public function getQBfindByCategory( $category )
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.categories', 'c')
            ->andWhere('c.id = '.$category->getId())
            ->andWhere('a.insertDate < :currentDate')
            ->setParameter('currentDate', date('Y-m-d H:i:s', time()))
            ->orderBy('a.insertDate', 'DESC');
    }

    public function getLast3ByCategory( $category )
    {
        return new ArrayCollection( $this->createQueryBuilder('a')
            ->innerJoin('a.categories', 'c')
            ->andWhere('c.id = '.$category->getId())
            ->andWhere('a.insertDate < :currentDate')
            ->setParameter('currentDate', date('Y-m-d H:i:s', time()))
            ->orderBy('a.insertDate', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult());
    }

    public function increaseViews( $article )
    {
        $qb = $this->createQueryBuilder('a');

        $q = $qb->update('App\Entity\Article', 'a')
            ->set('a.views', 'a.views + 1')
            ->where('a.id = ?1')
            ->setParameter(1, $article->getId())
            ->getQuery();
        $p = $q->execute();
    }

    public function getTopViewedForLastWeek()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.insertDate < :currentDate')
            ->setParameter('currentDate', date('Y-m-d H:i:s', time()))
            ->andWhere('a.insertDate > :dateWeekAgo')
            ->setParameter('dateWeekAgo', date('Y-m-d', strtotime('-7 days')))
            ->orderBy('a.views', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
