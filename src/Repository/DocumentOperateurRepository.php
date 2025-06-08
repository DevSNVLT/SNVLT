<?php

namespace App\Repository;

use App\Entity\References\DocumentOperateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentOperateur>
 *
 * @method DocumentOperateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentOperateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentOperateur[]    findAll()
 * @method DocumentOperateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentOperateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentOperateur::class);
    }

    /**
     * @return DocumentOperateur[] Returns an array of DocumentOperateur objects
     */
    public function searchDocOperateur($typeOperateur, $code_exploitant, $code_gl): array //
   {
        return $this->createQueryBuilder('d')
            ->andWhere('d.type_operateur = :typeOperateur')
            ->andWhere('d.codeOperateur = :code_exploitant')
            ->andWhere('d.code_document_grille = :code_gl')
            ->setParameter('typeOperateur', $typeOperateur)
            ->setParameter('code_exploitant', $code_exploitant)
            ->setParameter('code_gl', $code_gl)
            ->orderBy('d.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?DocumentOperateur
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
