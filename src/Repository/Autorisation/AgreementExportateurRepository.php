<?php

namespace App\Repository\Autorisation;

use App\Entity\Autorisation\AgreementExportateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AgreementExportateur>
 *
 * @method AgreementExportateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method AgreementExportateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method AgreementExportateur[]    findAll()
 * @method AgreementExportateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgreementExportateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AgreementExportateur::class);
    }

//    /**
//     * @return AgreementExportateur[] Returns an array of AgreementExportateur objects
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

//    public function findOneBySomeField($value): ?AgreementExportateur
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
