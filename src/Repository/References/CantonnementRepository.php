<?php

namespace App\Repository\References;

use App\Entity\References\Cantonnement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cantonnement>
 *
 * @method Cantonnement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cantonnement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cantonnement[]    findAll()
 * @method Cantonnement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CantonnementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cantonnement::class);
    }

    /**
     * @return Cantonnement[] Returns an array of Exploitant objects
     */
    public function findOnlyManager(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.email_personne_ressource is not null')
            ->orderBy('e.nom_cantonnement', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
//    /**
//     * @return Cantonnement[] Returns an array of Cantonnement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Cantonnement
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
