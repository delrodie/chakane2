<?php

namespace App\Repository;

use App\Entity\Produit;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Contracts\Cache\ItemInterface;

class AllRepository
{
    public function __construct(
        private CacheInterface $cache,
        private DeviseRepository $deviseRepository,
        private MarqueRepository $marqueRepository,
        private SlideRepository $slideRepository,
        private MaintenanceRepository $maintenanceRepository,
        private CollectionRepository $collectionRepository,
        private ContactRepository $contactRepository,
        private TypeRepository $typeRepository,
        private CategorieRepository $categorieRepository,
        private ProduitRepository $produitRepository,
        private RequestStack $requestStack
    )
    {
    }

    public function getOneDevise()
    {
        return $this->deviseRepository->findOneBy([],['id'=>"DESC"]);
    }

    public function getOneType(int $concerned=null)
    {
        if ($concerned) {
            return $this->typeRepository->findOneBy(['id' => $concerned]);
        }

        return $this->typeRepository->findOneBy([],['id'=>"DESC"]);
    }

    public function getOneCategorie(int $concerned=null)
    {
        if ($concerned) return $this->categorieRepository->findOneBy(['id'=>$concerned]);

        return $this->categorieRepository->findOneBy([],['id'=>"DESC"]);
    }

    public function getOneProduit(string $concerned=null)
    {
        if ($concerned) return $this->produitRepository->findOneBy(['slug'=>$concerned]);

        return $this->produitRepository->findOneBy([],['id'=>"DESC"]);
    }

    public function allCache(string $cacheName, bool $delete = false)
    {
        if ($delete) $this->cache->delete($cacheName);

        return $this->cache->get($cacheName, function (ItemInterface $item) use ($cacheName){
            $item->expiresAfter(604800); // 1 semaine(60*60*24*7)
            return $this->allRepositories($cacheName);
        });
    }

    public function allRepositories(string $cacheName)
    {
        return match ($cacheName) {
            'marque' => $this->marqueRepository->findOneBy([], ['id' => "DESC"]),
            'devise' => $this->getOneDevise(),
            'slides' => $this->slideRepository->findBy(['statut' => true], ['id' => "DESC"]),
            'maintenance' => $this->maintenanceRepository->findOneBy(['statut'=>true]),
            'collections' => $this->collectionRepository->findBy([],['id'=>"DESC"]),
            'contact' => $this->contactRepository->findOneBy([],['id'=>"DESC"]),
            'categories' => $this->categorieRepository->findBy([],['titre'=>"ASC"]),
            'produitsIdDesc' => $this->produitRepository->getProduisByIdDesc(),
            'newsProduits' => $this->produitRepository->getNewsProduitByFlagAndIdDesc(),
            'flagProduits' => $this->produitRepository->getProduitsByFlagDesc(),
            default => false,
        };

    }

    public function getProduitWithDevise(object $produit): array
    {
        $_devise = $this->requestStack->getSession()->get('devise');
        $currency = $this->allCache('devise');
        if ($_devise === 'EUR'){
            $solde = $produit->getSolde() ? $produit->getSolde() * $currency->getEuro() : null;
            $montant = $produit->getMontant() ? $produit->getMontant() * $currency->getEuro() : null;
        }elseif ($_devise === 'USD'){
            $solde = $produit->getSolde() ? $produit->getSolde() * $currency->getUsd() : null;
            $montant = $produit->getMontant() ? $produit->getMontant() * $currency->getUsd() : null;
        }
        else{
            $solde = $produit->getSolde();
            $montant = $produit->getMontant();
        } //dd($produit);

        return  [
            'id' => $produit->getId(),
            'reference' => $produit->getReference(),
            'titre' => $produit->getTitre(),
            'description' => $produit->getDescription(),
            'montant' => $montant,
            'solde' => $solde,
            'taille' => $produit->getTaille(),
            'couleur' => $produit->getCouleur(),
            'poids' => $produit->getPoids(),
            'media' => $produit->getMedia(),
            'photos' => $produit->getPhotos(),
            'flag' => $produit->getFlag(),
            'promotion' => $produit->isPromotion(),
            'stock' => $produit->getStock(),
            'tags' => $produit->getTags(),
            'conseil' => $produit->getConseil(),
            'slug' => $produit->getSlug(),
            'categories' => $produit->getCategories(),
            'images' => $produit->getProduitImages()
        ];
    }

    public function getProduitSimilaire(string $slug, bool $delete = false)
    {
        if ($delete) $this->cache->delete($slug);

        return$this->cache->get($slug, function (ItemInterface $item) use ($slug){
            $item->expiresAfter(604800); // 1 semaine (60*60*24*7)
            return $this->produitSimilaires($slug);
        });
    }

    private function produitSimilaires(string $slug): array
    {
        $produit = $this->produitRepository->getProduitBySlug($slug);

        if ($produit){
            $produits=[];
            foreach ($produit->getCategories() as $category){
                foreach ($category->getProduits() as $produit){
                    $produits[] = $this->getProduitWithDevise($produit);
                }
            }

            // Si les produits similaires sont moins de 5 alors ajouter d'autres produits
            if (count($produits) < 5){
                $nouveaux = $this->allCache('newsProduits');
                $autres=array_map([$this, 'getProduitWithDevise'], $nouveaux);

                return array_merge($produits, $autres);
            }

            shuffle($produits);
            return $produits;
        }

        return $this->getProduitsAleatoires();
    }

    public function getProduitsAleatoires(): array
    {
        $nouveaux = $this->allCache('newsProduits');
        $produits = array_map([$this, 'getProduitWithDevise'], $nouveaux);
        shuffle($produits);

        return $produits;
    }


}