<?php

namespace App\Repository\References;

use App\Entity\References\InventaireForestier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InventaireForestier>
 *
 * @method InventaireForestier|null find($id, $lockMode = null, $lockVersion = null)
 * @method InventaireForestier|null findOneBy(array $criteria, array $orderBy = null)
 * @method InventaireForestier[]    findAll()
 * @method InventaireForestier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InventaireForestierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InventaireForestier::class);
    }

//    /**
//     * @return InventaireForestier[] Returns an array of InventaireForestier objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?InventaireForestier
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
