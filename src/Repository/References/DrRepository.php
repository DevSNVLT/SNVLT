<?php

namespace App\Repository\References;

use App\Entity\References\Dr;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dr>
 *
 * @method Dr|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dr|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dr[]    findAll()
 * @method Dr[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DrRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dr::class);
    }
    /**
     * @return Dr[] Returns an array of Exploitant objects
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
//     * @return Dr[] Returns an array of Dr objects
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

//    public function findOneBySomeField($value): ?Dr
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
