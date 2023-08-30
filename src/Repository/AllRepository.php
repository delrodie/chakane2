<?php

namespace App\Repository;

use App\Entity\Produit;
use Knp\Component\Pager\PaginatorInterface;
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
        private RequestStack $requestStack,
        private CreationRepository $creationRepository,
        private PaginatorInterface $paginator
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
            'creations' => $this->creationRepository->findBy([],['id' => "DESC"]),
            default => false,
        };

    }

    public function getProduitWithDevise($produit): array
    {
        $_devise = $this->requestStack->getSession()->get('devise');
        $currency = $this->allCache('devise');
        if ($_devise === 'EUR'){ $produit->getMontant();
            $solde = $produit->getSolde() ? $produit->getSolde() * $currency->getEuro() : null;
            $montant = $produit->getMontant() ? $produit->getMontant() * $currency->getEuro() : null;
        }elseif ($_devise === 'USD'){
            $solde = $produit->getSolde() ? $produit->getSolde() * $currency->getUsd() : null;
            $montant = $produit->getMontant() ? $produit->getMontant() * $currency->getUsd() : null;
        }
        else{
            $solde = $produit->getSolde() ?: null;
            $montant = $produit->getMontant();
        } //dd(gettype($produit));

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

    public function getProduitWithDeviseArray($produit): array
    {
        $_devise = $this->requestStack->getSession()->get('devise');
        $currency = $this->allCache('devise');
        if ($_devise === 'EUR'){
            $solde = $produit['solde'] ? $produit['solde'] * $currency->getEuro() : null;
            $montant = $produit['montant'] ? $produit['montant'] * $currency->getEuro() : null;
        }elseif ($_devise === 'USD'){
            $solde = $produit['solde'] ? $produit['solde'] * $currency->getUsd() : null;
            $montant = $produit['montant'] ? $produit['montant'] * $currency->getUsd() : null;
        }
        else{
            $solde = $produit['solde'] ?: null;
            $montant = $produit['montant'];
        } //dd(gettype($produit));

        return  [
            'id' => $produit['id'],
            'reference' => $produit['reference'],
            'titre' => $produit['titre'],
            'description' => $produit['description'],
            'montant' => $montant,
            'solde' => $solde,
            'taille' => $produit['taille'],
            'couleur' => $produit['couleur'],
            'poids' => $produit['poids'],
            'media' => $produit['media'],
            'photos' => $produit['photos'],
            'flag' => $produit['flag'],
            'promotion' => $produit['promotion'],
            'stock' => $produit['stock'],
            'tags' => $produit['tags'],
            'conseil' => $produit['conseil'],
            'slug' => $produit['slug'],
            'categories' => $produit['categories'],
            'images' => $produit['images'],
        ];
    }

    public function getProduitSimilaire(string $slug, string $cacheName, bool $delete = false)
    {
        if ($delete) $this->cache->delete($cacheName);

        return $this->cache->get($cacheName, function (ItemInterface $item) use ($slug){
            $item->expiresAfter(604800); // 1 semaine (60*60*24*7)
            return $this->produitSimilaires($slug);
        });
    }

    private function produitSimilaires(string $slug): array
    {
        $produit = $this->produitRepository->getProduitBySlug($slug); //dd($produit);

        if ($produit){
            $produits=[]; $existingProduits=[];
            foreach ($produit->getCategories() as $category){
                foreach ($category->getProduits() as $produit){
                    if ($slug !== $produit->getSlug() && !in_array($produit->getSlug(), $existingProduits)) {
                        $produits[] = $this->getProduitWithDevise($produit);
                        $existingProduits[] = $produit->getSlug();
                    }
                }
            } //dd($produits);

            // Si les produits similaires sont moins de 5 alors ajouter d'autres produits
            if (count($produits) < 5){
                $nouveaux = $this->cacheDiversesRechercheProduits('newsProduits');
                $autres=array_map([$this, 'getProduitWithDeviseArray'], $nouveaux);

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

    public function getProduitsByCategorie(string $string, bool $delete = false)
    {
        if ($delete) $this->cache->delete($string);

        return $this->cache->get($string, function (ItemInterface $item) use ($string){
            $item->expiresAfter(604800); // 1 semaine
            return $this->produitsByCategorie($string);
        });
    }

    private function produitsByCategorie(string $string)
    {
        $produits = $this->produitRepository->getProduitByCategorie(substr($string, 0,3));

        return array_map([$this, 'getProduitWithDevise'], $produits);
    }

    public function getRandomCreation()
    {
        $creations = $this->allCache('creations');
        shuffle($creations);

        return $creations;
    }

    public function creationByPagination(int $per_page)
    {
        $creations = $this->allCache('creations');
        shuffle($creations);

        return $this->paginator->paginate(
            $creations,
            $this->requestStack->getCurrentRequest()->query->getInt('page', 1),
            $per_page
        );
    }

    public function cacheDiversesRechercheProduits(string $libelle, bool $delete = false)
    {
        $_devise = $this->requestStack->getSession()->get('devise');
        $cacheName = "{$libelle}-{$_devise}";

        if ($delete) $this->cache->delete($cacheName);

        return $this->cache->get($cacheName, function (ItemInterface $item) use($libelle){
            $item->expiresAfter(604800); // Une semaine 60*60*24*7
            return $this->getDiversesRechercheProduits($libelle);
        });
    }

    public function getDiversesRechercheProduits(string $libelle): array
    {
//        dd($this->produitRepository->getNewsProduitByFlagAndIdDesc());
        return match ($libelle){
            'newsProduits' => array_map([$this, 'getProduitWithDevise'], $this->produitRepository->getNewsProduitByFlagAndIdDesc()),
            'flagProduits' => array_map([$this, 'getProduitWithDevise'], $this->produitRepository->getProduitsByFlagDesc()),
            default => []
        };
    }

}