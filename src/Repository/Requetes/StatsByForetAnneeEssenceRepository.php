<?php

namespace App\Repository\Requetes;

use App\Entity\Requetes\StatsByForetAnneeEssence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatsByForetAnneeEssence>
 *
 * @method StatsByForetAnneeEssence|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatsByForetAnneeEssence|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatsByForetAnneeEssence[]    findAll()
 * @method StatsByForetAnneeEssence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class StatsByForetAnneeEssenceRepository  extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatsByForetAnneeEssence::class);
    }

    public function save(StatsByForetAnneeEssence $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(StatsByForetAnneeEssence $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}