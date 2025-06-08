<?php

namespace App\Repository\Admin;

use App\Entity\Admin\Option;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\This;

/**
 * @extends ServiceEntityRepository<Option>
 *
 * @method Option|null find($id, $lockMode = null, $lockVersion = null)
 * @method Option|null findOneBy(array $criteria, array $orderBy = null)
 * @method Option[]    findAll()
 * @method Option[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Option::class);
    }
/** @return Option[] */
    public function findAllForTwig() :array
    {
    return $this->createQueryBuilder('o', 'o.name')
        ->getQuery()
        ->getResult();

    }

    public function hasTVA() :?Option
    {
        $valeur = "tva_a_inclure";
        return $this->createQueryBuilder('o', 'o.name')
            ->andWhere('o.name = :val')
            ->setParameter('val', $valeur)
            ->getQuery()
            ->getOneOrNullResult();

    }
    public function findTVA() :?Option
    {
        $valeur = "tva_valeur";
        return $this->createQueryBuilder('o', 'o.name')
            ->andWhere('o.name = :val')
            ->setParameter('val', $valeur)
            ->getQuery()
            ->getOneOrNullResult();

    }
    public function getValue(string $name): mixed
    {
        try {
            return $this->createQueryBuilder('o')
                ->select('o.value')
                ->where('o.name = :name')
                ->setParameter('name', $name)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException){
            return null;
        }
    }
}
