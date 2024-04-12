<?php

namespace App\Repository;

use App\Entity\BlocNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BlocNote>
 *
 * @method BlocNote|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlocNote|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlocNote[]    findAll()
 * @method BlocNote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlocNoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlocNote::class);
    }

    //    /**
    //     * @return BlocNote[] Returns an array of BlocNote objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?BlocNote
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
