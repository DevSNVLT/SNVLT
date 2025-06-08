<?php

namespace App\Repository\DocStats\Saisie;

use App\Entity\DocStats\Saisie\Lignepagelje;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lignepagelje>
 *
 * @method Lignepagelje|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lignepagelje|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lignepagelje[]    findAll()
 * @method Lignepagelje[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LignepageljeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lignepagelje::class);
    }

//    /**
//     * @return Lignepagelje[] Returns an array of Lignepagelje objects
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

//    public function findOneBySomeField($value): ?Lignepagelje
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
