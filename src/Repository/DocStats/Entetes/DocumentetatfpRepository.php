<?php

namespace App\Repository\DocStats\Entetes;

use App\Entity\DocStats\Entetes\Documentetatfp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Documentetatfp>
 *
 * @method Documentetatfp|null find($id, $lockMode = null, $lockVersion = null)
 * @method Documentetatfp|null findOneBy(array $criteria, array $orderBy = null)
 * @method Documentetatfp[]    findAll()
 * @method Documentetatfp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentetatfpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Documentetatfp::class);
    }

//    /**
//     * @return Documentetatfp[] Returns an array of Documentetatfp objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Documentetatfp
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
