<?php

namespace App\Repository\Autorisation;

use App\Entity\Autorisation\AgreementPdtdrv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AgreementPdtdrv>
 *
 * @method AgreementPdtdrv|null find($id, $lockMode = null, $lockVersion = null)
 * @method AgreementPdtdrv|null findOneBy(array $criteria, array $orderBy = null)
 * @method AgreementPdtdrv[]    findAll()
 * @method AgreementPdtdrv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgreementPdtdrvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AgreementPdtdrv::class);
    }

//    /**
//     * @return AgreementPdtdrv[] Returns an array of AgreementPdtdrv objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AgreementPdtdrv
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
