<?php

namespace App\Repository\DocStats\Saisie;

use App\Entity\DocStats\Saisie\Lignepagebrh;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lignepagebrh>
 *
 * @method Lignepagebrh|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lignepagebrh|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lignepagebrh[]    findAll()
 * @method Lignepagebrh[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LignepagebrhRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lignepagebrh::class);
    }

//    /**
//     * @return Lignepagebrh[] Returns an array of Lignepagebrh objects
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

//    public function findOneBySomeField($value): ?Lignepagebrh
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
