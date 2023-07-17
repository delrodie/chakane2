<?php

namespace App\Repository;

use App\Entity\ConditionRetour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConditionRetour>
 *
 * @method ConditionRetour|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConditionRetour|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConditionRetour[]    findAll()
 * @method ConditionRetour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConditionRetourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConditionRetour::class);
    }

//    /**
//     * @return ConditionRetour[] Returns an array of ConditionRetour objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ConditionRetour
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
