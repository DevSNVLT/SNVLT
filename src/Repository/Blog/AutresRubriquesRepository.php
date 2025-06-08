<?php

namespace App\Repository\Blog;

use App\Entity\Blog\AutresRubriques;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AutresRubriques>
 *
 * @method AutresRubriques|null find($id, $lockMode = null, $lockVersion = null)
 * @method AutresRubriques|null findOneBy(array $criteria, array $orderBy = null)
 * @method AutresRubriques[]    findAll()
 * @method AutresRubriques[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AutresRubriquesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AutresRubriques::class);
    }

//    /**
//     * @return AutresRubriques[] Returns an array of AutresRubriques objects
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

//    public function findOneBySomeField($value): ?AutresRubriques
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
