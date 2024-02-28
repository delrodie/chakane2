<?php

namespace App\Service;

use App\Entity\Categorie;
use App\Entity\Devise;
use App\Entity\Produit;
use App\Repository\AllRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class Utility
{
    public function __construct(
        private EntityManagerInterface $_em,
        private AllRepository $allRepository,
        private CacheInterface $cache,
    )
    {
    }

    public function slug(string $string)
    {
        return (new AsciiSlugger())->slug(strtolower($string));
    }

    public function notification(string $message, string $type=null): void
    {
        $notif = notyf()
            ->position('x', 'center')
            ->position('y', 'top');

        switch ($type){
            case 'Error':
                $notif->addError($message);
                break;

            case 'Warning':
                $notif->addWarning($message);

            default:
                $notif->addSuccess($message);
        }

    }

    public function fuseauGMT(): \DateTime
    {
        // Définissons l'heure actuelle en utilisant le fuseau horaire GMT
        $fuseauGMT = new \DateTimeZone('GMT');
        return (new \DateTime('now', $fuseauGMT));
    }

    public function saveDevise(): object
    {
        $conversion = $this->getDevise();

        $entity = $this->allRepository->getOneDevise();
        $persist = false;
        if (!$entity){
            $entity = new Devise();
            $persist = true;
        }

        $entity->setXof(1);
        $entity->setEuro($conversion['EURO']);
        $entity->setUsd($conversion['USD']);
        $entity->setUpdatedAt($this->fuseauGMT());

        if ($persist) $this->_em->persist($entity);
        $this->_em->flush();

        return $entity;

    }

    /**
     * @return array|void
     */
    public function getDevise()
    {
        // https://app.exchangerate-api.com/dashboard
        // Fetching JSON
        $req_url = 'https://v6.exchangerate-api.com/v6/cf88c3d5060e96dd04772bf7/latest/XOF';
        $response_json = file_get_contents($req_url);

        // Continuing if we got a result
        if(false !== $response_json) {

            // Try/catch for json_decode operation
            try {

                // Decoding
                $response = json_decode($response_json);

                // Check for success
                if('success' === $response->result) {

                    // YOUR APPLICATION CODE HERE, e.g.
                    $base_price = 1; // Your price in XOF

                    return [
                        'EURO' => round(($base_price * $response->conversion_rates->EUR),6),
                        'USD' => round(($base_price * $response->conversion_rates->USD),6),
                    ];

                }

            }
            catch(Exception $e) {
                // Handle JSON parse error...
            }

        }
    }

    public function getUserCountry(?string $userIp): ?string
    {
        try {
            $httpClient = HttpClient::create();
            $response = $httpClient->request('GET', 'https://ipinfo.io/' . $userIp . '/country');
            // Vous pouvez utiliser une base de données ou un service tiers pour obtenir le nom complet du pays à partir du code du pays.
            // Par exemple, vous pouvez utiliser le service "ipinfo.io" pour obtenir des informations plus détaillées comme le nom complet du pays.
            // curl "ipinfo.io/102.67.254.134?token=34390da7c9562a"
            return $response->getContent(); // Par exemple, 'US' pour les États-Unis, 'FR' pour la France, etc.
        } catch (\Exception $e) {
            // En cas d'erreur lors de la récupération du pays, retournez NULL ou un pays par défaut.
            return null;
        }
    }

    public function getUserLocation(?string $userIp)
    {

        // Essayons d'obtenir les données de localisation à partir du cache
        return $this->cache->get('user_location_'.$userIp, function (ItemInterface $item) use($userIp){
            $item->expiresAfter(86400); // 24h
            return $this->getUserLocationFromApi($userIp);
        });

    }

    public function getUserLocationFromApi(string $userIp): ?string
    {
        try{
            $httpClient = HttpClient::create();
            $response = $httpClient->request('GET', "https://ipinfo.io/{$userIp}?token=34390da7c9562a");
            return $response->getContent();
        }catch (\Exception $e){
            return null;
        }
    }

    public function codeType(): int|string|null
    {
        $type = $this->allRepository->getOneType();
        if (!$type) return 10;

        return $type->getCode() + 1;
    }

    public function codeCategorie(Categorie $categorie): void
    {
        $entity = $this->allRepository->getOneCategorie();
//        $code = $categorie->getCode() ? $categorie->getCode() + 1 : '20';

//        $code = $codeType . ($categorie->getId() ? $categorie->getId() + 10 : ($entity ? $entity->getId() + 11 : '11'));

        $categorie->setCode($entity ? $entity->getCode() +1 : '20');

        $categorie->setSlug($this->slug($categorie->getTitre()));
    }

    public function codeProduit(Produit $produit): bool
    {
        $codeCategorie = round(1001,1999);
        $slug = $this->slug($produit->getTitre());
        $codeType = (int) ($produit->getType() ? $produit->getType()->getCode(): 10);

        if (!$produit->getId()){
            // Verification de la non-existence du slug dans la base de données
            if ($this->allRepository->getOneProduit($slug)) return false;

            $entity = $this->allRepository->getOneProduit();
            $reference = $codeType . ($entity ? $entity->getId() + 1001 : '1001');

            $produit->setReference($reference);
        }

        $produit->setSlug($slug);

        return true;
    }

    public function codeClient()
    {
        $lastClient = $this->allRepository->getOneClient();
        $aleatoire = date('ym');

        $lastId = $lastClient ? (int) $lastClient->getId() : 0;
        $newId = $lastId + 1;

        // Ajout de 0 à gauche jusqu'à atteindre les 5 position
        $id = str_pad($newId, 5, '0', STR_PAD_LEFT);

        return $aleatoire.$id;
    }

}