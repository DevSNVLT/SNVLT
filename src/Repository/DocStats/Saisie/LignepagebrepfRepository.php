<?php

namespace App\Repository\DocStats\Saisie;

use App\Entity\DocStats\Saisie\Lignepagebrepf;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lignepagebrepf>
 *
 * @method Lignepagebrepf|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lignepagebrepf|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lignepagebrepf[]    findAll()
 * @method Lignepagebrepf[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LignepagebrepfRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lignepagebrepf::class);
    }

//    /**
//     * @return Lignepagebrepf[] Returns an array of Lignepagebrepf objects
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

//    public function findOneBySomeField($value): ?Lignepagebrepf
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
