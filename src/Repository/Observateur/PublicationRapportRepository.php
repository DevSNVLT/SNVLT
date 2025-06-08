<?php

namespace App\Repository\Observateur;

use App\Entity\Observateur\PublicationRapport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PublicationRapport>
 *
 * @method PublicationRapport|null find($id, $lockMode = null, $lockVersion = null)
 * @method PublicationRapport|null findOneBy(array $criteria, array $orderBy = null)
 * @method PublicationRapport[]    findAll()
 * @method PublicationRapport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicationRapportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PublicationRapport::class);
    }

    /**
     * @return PublicationRapport[] Returns an array of PublicationRapport objects
     */
    public function findByStatut(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.statut as statut, count(p.id) as nombre')
            ->orderBy('p.statut', 'ASC')
            ->groupBy('p.statut')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return PublicationRapport[] Returns an array of PublicationRapport objects
     */

//    public function findOneBySomeField($value): ?PublicationRapport
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
