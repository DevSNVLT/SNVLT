<?php

namespace App\Repository\References;

use App\Entity\References\NaturePs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NaturePs>
 *
 * @method NaturePs|null find($id, $lockMode = null, $lockVersion = null)
 * @method NaturePs|null findOneBy(array $criteria, array $orderBy = null)
 * @method NaturePs[]    findAll()
 * @method NaturePs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NaturePsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NaturePs::class);
    }

//    /**
//     * @return NaturePs[] Returns an array of NaturePs objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?NaturePs
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
