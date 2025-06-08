<?php

namespace App\Repository\Observateur;

use App\Entity\Observateur\TicketFiles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TicketFiles>
 *
 * @method TicketFiles|null find($id, $lockMode = null, $lockVersion = null)
 * @method TicketFiles|null findOneBy(array $criteria, array $orderBy = null)
 * @method TicketFiles[]    findAll()
 * @method TicketFiles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketFilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketFiles::class);
    }

//    /**
//     * @return TicketFiles[] Returns an array of TicketFiles objects
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

//    public function findOneBySomeField($value): ?TicketFiles
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
