<?php

namespace App\Repository\DocStats\Pages;

use App\Entity\DocStats\Pages\Lignepagebcbp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lignepagebcbp>
 *
 * @method Lignepagebcbp|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lignepagebcbp|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lignepagebcbp[]    findAll()
 * @method Lignepagebcbp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LignepagebcbpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lignepagebcbp::class);
    }

//    /**
//     * @return Lignepagebcbp[] Returns an array of Lignepagebcbp objects
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

//    public function findOneBySomeField($value): ?Lignepagebcbp
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
