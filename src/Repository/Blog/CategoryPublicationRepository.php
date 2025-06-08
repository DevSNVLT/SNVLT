<?php

namespace App\Repository\Blog;

use App\Entity\Blog\CategoryPublication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategoryPublication>
 *
 * @method CategoryPublication|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryPublication|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryPublication[]    findAll()
 * @method CategoryPublication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryPublicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryPublication::class);
    }

//    /**
//     * @return CategoryPublication[] Returns an array of CategoryPublication objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CategoryPublication
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
