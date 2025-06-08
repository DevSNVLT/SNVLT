<?php

namespace App\Repository\References;

use App\Entity\References\TypeDocumentStatistique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeDocumentStatistique>
 *
 * @method TypeDocumentStatistique|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeDocumentStatistique|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeDocumentStatistique[]    findAll()
 * @method TypeDocumentStatistique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeDocumentStatistiqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeDocumentStatistique::class);
    }

//    /**
//     * @return TypeDocumentStatistique[] Returns an array of TypeDocumentStatistique objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TypeDocumentStatistique
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
