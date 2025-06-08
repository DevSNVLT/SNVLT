<?php

namespace App\Repository\References;

use App\Entity\References\Ugf;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ugf>
 *
 * @method Ugf|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ugf|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ugf[]    findAll()
 * @method Ugf[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UgfRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ugf::class);
    }

//    /**
//     * @return Ugf[] Returns an array of Ugf objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Ugf
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
