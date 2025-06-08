<?php

namespace App\Repository\DocStats\Entetes;

use App\Entity\DocStats\Entetes\Documentetate2;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Documentetate2>
 *
 * @method Documentetate2|null find($id, $lockMode = null, $lockVersion = null)
 * @method Documentetate2|null findOneBy(array $criteria, array $orderBy = null)
 * @method Documentetate2[]    findAll()
 * @method Documentetate2[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Documentetate2Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Documentetate2::class);
    }

//    /**
//     * @return Documentetate2[] Returns an array of Documentetate2 objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Documentetate2
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
