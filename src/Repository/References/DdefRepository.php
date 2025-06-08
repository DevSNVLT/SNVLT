<?php

namespace App\Repository\References;

use App\Entity\References\Ddef;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ddef>
 *
 * @method Ddef|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ddef|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ddef[]    findAll()
 * @method Ddef[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DdefRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ddef::class);
    }
    /**
     * @return Ddef[] Returns an array of Exploitant objects
     */
    public function findOnlyManager(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.email_personne_ressource is not null')
            ->orderBy('e.nom_ddef', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
//    /**
//     * @return Ddef[] Returns an array of Ddef objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Ddef
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
