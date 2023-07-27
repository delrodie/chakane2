<?php

namespace App\Repository;

use App\Entity\MessageInternaute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MessageInternaute>
 *
 * @method MessageInternaute|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageInternaute|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageInternaute[]    findAll()
 * @method MessageInternaute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageInternauteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageInternaute::class);
    }

//    /**
//     * @return MessageInternaute[] Returns an array of MessageInternaute objects
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

//    public function findOneBySomeField($value): ?MessageInternaute
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
