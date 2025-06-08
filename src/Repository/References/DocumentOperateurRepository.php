<?php

namespace App\Repository\References;

use App\Entity\References\DocumentOperateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentOperateur>
 *
 * @method DocumentOperateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentOperateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentOperateur[]    findAll()
 * @method DocumentOperateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentOperateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentOperateur::class);
    }

//    /**
//     * @return DocumentOperateur[] Returns an array of DocumentOperateur objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DocumentOperateur
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
