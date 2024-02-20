<?php

namespace App\Service;

use App\Repository\AllRepository;
use App\Repository\DeviseRepository;
use App\Repository\ProduitRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class AllCaches
{
    public function __construct(
        private CacheInterface $cache,
        private RequestStack $requestStack,
        private PaginatorInterface $paginator,
        private DeviseRepository $deviseRepository,
        private ProduitRepository $produitRepository,
        private AllRepository $allRepository
    )
    {
    }

    // Gestiond des produits par type
    public function paginationProduistByType(string $libelle, int $par_page)
    {
        return $this->paginator->paginate(
            $this->getProduitsByType($libelle),
            $this->requestStack->getCurrentRequest()->query->getInt('page', 1),
            $par_page
        );
    }

    public function getProduitsByType(string $libelle, bool $delete = false )
    {
        $_devise = $this->requestStack->getSession()->get('devise');
        $devises = $_devise ? [$_devise] : ['EUR','USD','XOF'];

        $produits=[];
        foreach ($devises as $devise){
            $cacheName = "{$libelle}-{$devise}";
            $produits =  $this->cacheProduitByType($cacheName, $libelle, $delete ?: false);
        }

        return $produits;
    }

    public function cacheProduitByType(string $cacheName, string $libelle, bool $delete = false)
    {
        if ($delete) $this->cache->delete($cacheName);

        return $this->cache->get($cacheName, function (ItemInterface $item) use($libelle){
            $item->expiresAfter(604800);
            return array_map(
                [$this->allRepository, 'getProduitWithDevise'],
                $this->produitRepository->getProduitByType(substr($libelle,0, 3))
            );
        });
    }

    // Gestion des produits par des catégories
    public function paginationProduitsByCategorie(string $string, int $par_page = 10)
    {
        return $this->paginator->paginate(
            $this->cacheProduitsByCategorie($string),
            $this->requestStack->getCurrentRequest()->query->getInt('page', 1),
            $par_page
        );
    }

    public function cacheProduitsByCategorie(string $string, bool $delete = false)
    {
        if ($delete) $this->cache->delete($string);
        return $this->cache->get($string, function (ItemInterface $item) use ($string){
            $item->expiresAfter(604800);
            return array_map(
                [$this->allRepository, 'getProduitWithDevise'],
                $this->produitRepository->getProduitByCategorie(substr($string, 0, 3))
            );
        });
    }


    // Gestion des nouveaux et flag produits
    public function paginationDiversesRechercheProduits(string $string, int $per_page)
    {
        return $this->paginator->paginate(
            $this->allRepository->cacheDiversesRechercheProduits($string),
            $this->requestStack->getCurrentRequest()->query->getInt('page', 1),
            $per_page
        );
    }

}