<?php

namespace App\Repository\DocStats;

use App\Entity\DocStats\Saisie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Saisie>
 *
 * @method Saisie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Saisie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Saisie[]    findAll()
 * @method Saisie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SaisieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Saisie::class);
    }

//    /**
//     * @return Saisie[] Returns an array of Saisie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Saisie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
