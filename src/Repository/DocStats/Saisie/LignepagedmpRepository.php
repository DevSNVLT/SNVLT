<?php

namespace App\Repository\DocStats\Saisie;

use App\Entity\DocStats\Saisie\Lignepagedmp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lignepagedmp>
 *
 * @method Lignepagedmp|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lignepagedmp|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lignepagedmp[]    findAll()
 * @method Lignepagedmp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LignepagedmpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lignepagedmp::class);
    }

//    /**
//     * @return Lignepagedmp[] Returns an array of Lignepagedmp objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Lignepagedmp
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
