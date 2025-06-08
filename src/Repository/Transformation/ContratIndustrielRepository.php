<?php

namespace App\Repository\Transformation;

use App\Entity\Transformation\ContratIndustriel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContratIndustriel>
 *
 * @method ContratIndustriel|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContratIndustriel|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContratIndustriel[]    findAll()
 * @method ContratIndustriel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContratIndustrielRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContratIndustriel::class);
    }

//    /**
//     * @return ContratIndustriel[] Returns an array of ContratIndustriel objects
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

//    public function findOneBySomeField($value): ?ContratIndustriel
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
