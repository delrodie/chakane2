<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function getProduisByIdDesc()
    {
        return $this->createQueryBuilder('p')
            ->addSelect('c')
            ->leftJoin('p.categories', 'c')
            ->orderBy('p.id', 'DESC')
            ->getQuery()->getResult()
            ;
    }

    public function getNewsProduitByFlagAndIdDesc()
    {
        return $this->createQueryBuilder('p')
            ->addSelect('c')
            ->addSelect('i')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('p.produitImages', 'i')
            ->orderBy('p.id', "DESC")
            ->addOrderBy('p.flag', "DESC")
            ->getQuery()->getResult()
            ;
    }

    public function getProduitsByFlagDesc()
    {
        return $this->createQueryBuilder('p')
            ->addSelect('c')
            ->addSelect('i')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('p.produitImages', 'i')
            ->orderBy('p.flag', "DESC")
            ->getQuery()->getResult();
    }

//    /**
//     * @return Produit[] Returns an array of Produit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Produit
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
