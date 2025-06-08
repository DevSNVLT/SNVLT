<?php

namespace App\Repository\Autorisations;

use App\Entity\Autorisation\AgreementPs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AgreementPs>
 *
 * @method AgreementPs|null find($id, $lockMode = null, $lockVersion = null)
 * @method AgreementPs|null findOneBy(array $criteria, array $orderBy = null)
 * @method AgreementPs[]    findAll()
 * @method AgreementPs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgreementPsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AgreementPs::class);
    }

//    /**
//     * @return AgreementPs[] Returns an array of AgreementPs objects
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

//    public function findOneBySomeField($value): ?AgreementPs
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
