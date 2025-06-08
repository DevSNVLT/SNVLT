<?php

namespace App\Repository\References;

use App\Entity\References\PosteForestier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PosteForestier>
 *
 * @method PosteForestier|null find($id, $lockMode = null, $lockVersion = null)
 * @method PosteForestier|null findOneBy(array $criteria, array $orderBy = null)
 * @method PosteForestier[]    findAll()
 * @method PosteForestier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PosteForestierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PosteForestier::class);
    }

    /**
     * @return PosteForestier[] Returns an array of Exploitant objects
     */
    public function findOnlyManager(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.email_personne_ressource is not null')
            ->orderBy('e.denomination', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
//    /**
//     * @return PosteForestier[] Returns an array of PosteForestier objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PosteForestier
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
