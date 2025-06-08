<?php

namespace App\Repository\DocStats\Saisie;

use App\Entity\DocStats\Saisie\Lignepagebcbp;
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
//     * @return LIgnepagebcbp[] Returns an array of LIgnepagebcbp objects
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

//    public function findOneBySomeField($value): ?LIgnepagebcbp
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
