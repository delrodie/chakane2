<?php

namespace App\Repository;

use App\Entity\MentionLegale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MentionLegale>
 *
 * @method MentionLegale|null find($id, $lockMode = null, $lockVersion = null)
 * @method MentionLegale|null findOneBy(array $criteria, array $orderBy = null)
 * @method MentionLegale[]    findAll()
 * @method MentionLegale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MentionLegaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MentionLegale::class);
    }

//    /**
//     * @return MentionLegale[] Returns an array of MentionLegale objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MentionLegale
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
