<?php

namespace App\Repository\DocStats\Entetes;

use App\Entity\DocStats\Entetes\Documentbcbp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Documentbcbp>
 *
 * @method Documentbcbp|null find($id, $lockMode = null, $lockVersion = null)
 * @method Documentbcbp|null findOneBy(array $criteria, array $orderBy = null)
 * @method Documentbcbp[]    findAll()
 * @method Documentbcbp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentbcbpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Documentbcbp::class);
    }

//    /**
//     * @return Documentbcbp[] Returns an array of Documentbcbp objects
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

//    public function findOneBySomeField($value): ?Documentbcbp
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
