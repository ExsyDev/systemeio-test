<?php

namespace App\Repository;

use App\Entity\Taxes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Taxes>
 *
 * @method Taxes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Taxes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Taxes[]    findAll()
 * @method Taxes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxesRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Taxes::class);
    }

    /**
     * @param Taxes $entity
     * @param bool $flush
     * @return void
     */
    public function save(Taxes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Taxes $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Taxes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
