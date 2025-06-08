<?php

namespace App\Repository\Blog;

use App\Entity\Blog\GroupePublication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupePublication>
 *
 * @method GroupePublication|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupePublication|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupePublication[]    findAll()
 * @method GroupePublication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupePublicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupePublication::class);
    }

//    /**
//     * @return GroupePublication[] Returns an array of GroupePublication objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GroupePublication
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
