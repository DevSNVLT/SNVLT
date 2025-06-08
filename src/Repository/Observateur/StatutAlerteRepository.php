<?php

namespace App\Repository\Observateur;

use App\Entity\Observateur\StatutAlerte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatutAlerte>
 *
 * @method StatutAlerte|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatutAlerte|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatutAlerte[]    findAll()
 * @method StatutAlerte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatutAlerteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatutAlerte::class);
    }

//    /**
//     * @return StatutAlerte[] Returns an array of StatutAlerte objects
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

//    public function findOneBySomeField($value): ?StatutAlerte
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
