<?php

namespace App\Repository\Observateur;

use App\Entity\Observateur\TypeRapportOi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeRapportOi>
 *
 * @method TypeRapportOi|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeRapportOi|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeRapportOi[]    findAll()
 * @method TypeRapportOi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeRapportOiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeRapportOi::class);
    }

//    /**
//     * @return TypeRapportOi[] Returns an array of TypeRapportOi objects
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

//    public function findOneBySomeField($value): ?TypeRapportOi
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
