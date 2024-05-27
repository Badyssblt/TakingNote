<?php

namespace App\Repository;

use App\Entity\Friends;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Friends>
 *
 * @method Friends|null find($id, $lockMode = null, $lockVersion = null)
 * @method Friends|null findOneBy(array $criteria, array $orderBy = null)
 * @method Friends[]    findAll()
 * @method Friends[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friends::class);
    }

    public function getFriendsQuery(User $user, string $status)
    {
        if ($status === "accepted") {
            return $this->createQueryBuilder('f')
                ->andWhere('f.user1 = :user')
                ->orWhere('f.user2 = :user')
                ->andWhere("f.status = :status")
                ->setParameter('user', $user)
                ->setParameter(':status', $status)
                ->getQuery()
                ->getResult();
        } else if ($status === "pending") {
            return $this->createQueryBuilder('f')
                ->andWhere('f.user1 = :user')
                ->orWhere('f.user2 = :user')
                ->andWhere("f.status = :status")
                ->andWhere('f.sender != :user')
                ->setParameter('user', $user)
                ->setParameter(':status', $status)
                ->getQuery()
                ->getResult();
        }
    }

    //    /**
    //     * @return Friends[] Returns an array of Friends objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Friends
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
