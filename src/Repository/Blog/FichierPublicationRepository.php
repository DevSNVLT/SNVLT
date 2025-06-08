<?php

namespace App\Repository\Blog;

use App\Entity\Blog\FichierPublication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FichierPublication>
 *
 * @method FichierPublication|null find($id, $lockMode = null, $lockVersion = null)
 * @method FichierPublication|null findOneBy(array $criteria, array $orderBy = null)
 * @method FichierPublication[]    findAll()
 * @method FichierPublication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FichierPublicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FichierPublication::class);
    }

//    /**
//     * @return FichierPublication[] Returns an array of FichierPublication objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FichierPublication
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
