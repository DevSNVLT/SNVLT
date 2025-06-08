<?php

namespace App\Repository\References;

use App\Entity\References\CircuitCommunication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CircuitCommunication>
 *
 * @method CircuitCommunication|null find($id, $lockMode = null, $lockVersion = null)
 * @method CircuitCommunication|null findOneBy(array $criteria, array $orderBy = null)
 * @method CircuitCommunication[]    findAll()
 * @method CircuitCommunication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CircuitCommunicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CircuitCommunication::class);
    }

    /**
     * @return CircuitCommunication[] Returns an array of CircuitCommunication objects
     */
    public function findCircuitByDocument($value): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.code_document_operateur = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findCircuitByDemande($value): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.code_demande_operateur = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
//    public function findOneBySomeField($value): ?CircuitCommunication
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
