<?php

namespace App\Repository\DocStats\Entetes;

use App\Entity\DocStats\Entetes\Documentbcburb;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Documentbcburb>
 *
 * @method Documentbcburb|null find($id, $lockMode = null, $lockVersion = null)
 * @method Documentbcburb|null findOneBy(array $criteria, array $orderBy = null)
 * @method Documentbcburb[]    findAll()
 * @method Documentbcburb[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentbcburbRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Documentbcburb::class);
    }

//    /**
//     * @return Documentbcburb[] Returns an array of Documentbcburb objects
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

//    public function findOneBySomeField($value): ?Documentbcburb
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
