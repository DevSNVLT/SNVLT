<?php

namespace App\Repository\DocStats\Saisie;

use App\Entity\DocStats\Saisie\Lignepagedmv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lignepagedmv>
 *
 * @method Lignepagedmv|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lignepagedmv|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lignepagedmv[]    findAll()
 * @method Lignepagedmv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LignepagedmvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lignepagedmv::class);
    }

//    /**
//     * @return Lignepagedmv[] Returns an array of Lignepagedmv objects
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

//    public function findOneBySomeField($value): ?Lignepagedmv
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
