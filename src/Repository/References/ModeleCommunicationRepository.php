<?php

namespace App\Repository\References;

use App\Entity\References\ModeleCommunication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ModeleCommunication>
 *
 * @method ModeleCommunication|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModeleCommunication|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModeleCommunication[]    findAll()
 * @method ModeleCommunication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModeleCommunicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModeleCommunication::class);
    }

//    /**
//     * @return ModeleCommunication[] Returns an array of ModeleCommunication objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ModeleCommunication
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
