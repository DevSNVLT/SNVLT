<?php

namespace App\Repository\References;

use App\Entity\References\ProduitsUsine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProduitsUsine>
 *
 * @method ProduitsUsine|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProduitsUsine|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProduitsUsine[]    findAll()
 * @method ProduitsUsine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitsUsineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProduitsUsine::class);
    }

//    /**
//     * @return ProduitsUsine[] Returns an array of ProduitsUsine objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ProduitsUsine
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
