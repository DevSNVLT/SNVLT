<?php

namespace App\Repository\DocStats\Entetes;

use App\Entity\DocStats\Entetes\Documentdmv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Documentdmv>
 *
 * @method Documentdmv|null find($id, $lockMode = null, $lockVersion = null)
 * @method Documentdmv|null findOneBy(array $criteria, array $orderBy = null)
 * @method Documentdmv[]    findAll()
 * @method Documentdmv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentdmvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Documentdmv::class);
    }

//    /**
//     * @return Documentdmv[] Returns an array of Documentdmv objects
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

//    public function findOneBySomeField($value): ?Documentdmv
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
