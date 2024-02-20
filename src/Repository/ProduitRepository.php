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

    public function getProduitBySlug(string $slug)
    {
        return $this->createQueryBuilder('p')
            ->addSelect('c')
            ->addSelect('i')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('p.produitImages', 'i')
            ->where('p.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()->getOneOrNullResult();
    }

    public function getProduitByCategorie(string $string)
    {
        return $this->createQueryBuilder('p')
            ->addSelect('c')
            ->addSelect('i')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('p.produitImages', 'i')
            ->where('c.slug LIKE :slug')
            ->orderBy('p.flag', "DESC")
            ->addOrderBy('p.id', "DESC")
            ->setParameter('slug', "%{$string}%")
            ->getQuery()->getResult();
    }

    public function getProduitByType(string $libelle)
    {
        return $this->createQueryBuilder('p')
            ->addSelect('c')
            ->addSelect('i')
            ->addSelect('t')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('p.produitImages', 'i')
            ->leftJoin('p.type', 't')
            ->where('t.titre LIKE :libelle')
            ->orderBy('p.flag', "DESC")
            ->addOrderBy('p.id', "DESC")
            ->setParameter('libelle', "%{$libelle}%")
            ->getQuery()->getResult();
    }
}
