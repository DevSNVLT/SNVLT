<?php

namespace App\Repository\DocStats\Entetes;

use App\Entity\DocStats\Entetes\Documentbrepf;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Documentbrepf>
 *
 * @method Documentbrepf|null find($id, $lockMode = null, $lockVersion = null)
 * @method Documentbrepf|null findOneBy(array $criteria, array $orderBy = null)
 * @method Documentbrepf[]    findAll()
 * @method Documentbrepf[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentbrepfRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Documentbrepf::class);
    }

//    /**
//     * @return Documentbrepf[] Returns an array of Documentbrepf objects
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

//    public function findOneBySomeField($value): ?Documentbrepf
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
