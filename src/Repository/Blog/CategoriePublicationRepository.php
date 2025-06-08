<?php

namespace App\Repository\Blog;

use App\Entity\Blog\CategoriePublication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategoriePublication>
 *
 * @method CategoriePublication|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoriePublication|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoriePublication[]    findAll()
 * @method CategoriePublication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoriePublicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoriePublication::class);
    }

//    /**
//     * @return CategoriePublication[] Returns an array of CategoriePublication objects
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

//    public function findOneBySomeField($value): ?CategoriePublication
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
