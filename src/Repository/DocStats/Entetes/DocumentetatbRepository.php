<?php

namespace App\Repository\DocStats\Entetes;

use App\Entity\DocStats\Entetes\Documentetatb;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Documentetatb>
 *
 * @method Documentetatb|null find($id, $lockMode = null, $lockVersion = null)
 * @method Documentetatb|null findOneBy(array $criteria, array $orderBy = null)
 * @method Documentetatb[]    findAll()
 * @method Documentetatb[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentetatbRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Documentetatb::class);
    }

//    /**
//     * @return Documentetatb[] Returns an array of Documentetatb objects
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

//    public function findOneBySomeField($value): ?Documentetatb
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
