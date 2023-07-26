<?php

namespace App\Repository;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
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
        private MaintenanceRepository $maintenanceRepository
    )
    {
    }

    public function getOneDevise()
    {
        return $this->deviseRepository->findOneBy([],['id'=>"DESC"]);
    }

    public function allCache(string $cacheName, bool $delete = false)
    {
        if ($delete) $this->cache->delete($cacheName);

        return $this->cache->get($cacheName, function (ItemInterface $item) use ($cacheName){
            $item->expiresAfter(86400);
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
            default => false,
        };

    }

}