<?php

namespace App\Repository\Autorisation;

use App\Entity\Autorisation\AutorisationExportateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AutorisationExportateur>
 *
 * @method AutorisationExportateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method AutorisationExportateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method AutorisationExportateur[]    findAll()
 * @method AutorisationExportateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AutorisationExportateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AutorisationExportateur::class);
    }

//    /**
//     * @return AutorisationExportateur[] Returns an array of AutorisationExportateur objects
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

//    public function findOneBySomeField($value): ?AutorisationExportateur
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
