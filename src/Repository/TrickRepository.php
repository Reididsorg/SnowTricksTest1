<?php


namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    public function getTricks(int $offset, int $limit)
    {
        $qb = $this->createQueryBuilder('t')
            ->orderBy('t.createdAt', 'DESC')
            //->orderBy('t.id', 'ASC')
            ->getQuery();

        $qb->setFirstResult($offset)
            ->setMaxResults($limit);

        return $qb->getResult();
    }

    /**
     * @return Trick[] Returns an array of Trick objects
     */
    public function getAllTricks()
    {
        return $this->findAll();
    }
}