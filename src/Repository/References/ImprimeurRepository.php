<?php

namespace App\Repository\References;

use App\Entity\References\Imprimeur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Imprimeur>
 *
 * @method Imprimeur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Imprimeur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Imprimeur[]    findAll()
 * @method Imprimeur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImprimeurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Imprimeur::class);
    }

//    /**
//     * @return Imprimeur[] Returns an array of Imprimeur objects
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

//    public function findOneBySomeField($value): ?Imprimeur
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
