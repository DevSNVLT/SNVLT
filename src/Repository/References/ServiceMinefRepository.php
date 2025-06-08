<?php

namespace App\Repository\References;

use App\Entity\References\ServiceMinef;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServiceMinef>
 *
 * @method ServiceMinef|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceMinef|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceMinef[]    findAll()
 * @method ServiceMinef[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceMinefRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceMinef::class);
    }

//    /**
//     * @return ServiceMinef[] Returns an array of ServiceMinef objects
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

//    public function findOneBySomeField($value): ?ServiceMinef
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
