<?php

namespace App\Service;

use App\Entity\Devise;
use App\Repository\AllRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\String\Slugger\AsciiSlugger;

class Utility
{
    public function __construct(
        private EntityManagerInterface $_em,
        private AllRepository $allRepository
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

            return $response->getContent(); // Par exemple, 'US' pour les États-Unis, 'FR' pour la France, etc.
        } catch (\Exception $e) {
            // En cas d'erreur lors de la récupération du pays, retournez NULL ou un pays par défaut.
            return null;
        }
    }

    public function codeType(): int|string|null
    {
        $type = $this->allRepository->getOneType();
        if (!$type) return 10;

        return $type->getCode() + 1;
    }
}