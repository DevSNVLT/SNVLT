<?php

namespace App\Repository\References;

use App\Entity\References\TypeDossierPs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeDossierPs>
 *
 * @method TypeDossierPs|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeDossierPs|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeDossierPs[]    findAll()
 * @method TypeDossierPs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeDossierPsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeDossierPs::class);
    }

//    /**
//     * @return TypeDossierPs[] Returns an array of TypeDossierPs objects
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

//    public function findOneBySomeField($value): ?TypeDossierPs
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
