<?php

namespace App\Repository\Admin;

use App\Entity\Admin\LogSnvlt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogSnvlt>
 *
 * @method LogSnvlt|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogSnvlt|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogSnvlt[]    findAll()
 * @method LogSnvlt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogSnvltRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogSnvlt::class);
    }

//    /**
//     * @return LogSnvlt[] Returns an array of LogSnvlt objects
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

//    public function findOneBySomeField($value): ?LogSnvlt
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
