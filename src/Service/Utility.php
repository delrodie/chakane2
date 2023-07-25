<?php

namespace App\Service;

use App\Entity\Devise;
use App\Repository\AllRepository;
use Doctrine\ORM\EntityManagerInterface;
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
}