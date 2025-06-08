<?php

namespace App\Repository\References;

use App\Entity\References\Exportateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Exportateur>
 *
 * @method Exportateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Exportateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Exportateur[]    findAll()
 * @method Exportateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExportateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exportateur::class);
    }

    /**
     * @return Exportateur[] Returns an array of Exploitant objects
     */
    public function findOnlyManager(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.email_personne_ressource is not null')
            ->orderBy('e.raison_sociale_exportateur', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
//    /**
//     * @return Exportateur[] Returns an array of Exportateur objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Exportateur
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
