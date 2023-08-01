<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class UserActivityLogger
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private string $logFilePath,
        private RequestStack $requestStack,
        private Utility $utility
    )
    {
        $this->createdLogFileNeeded();
    }

    private function createdLogFileNeeded(): void
    {
        if (!file_exists($this->logFilePath)){
            $directory = dirname($this->logFilePath);

            if (!mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new \RuntimeException(sprintf('Le repertoire "%s" n\'a pas été crée' , $directory));
            }

            touch($this->logFilePath);
        }
    }

    public function logActivity(string $action): void
    {
        $token = $this->tokenStorage->getToken(); //dd($user);
        $user = $token?->getUser();
        $username = $user ? $user->getUserIdentifier() : 'Anonyme';

        // Récuperation de l'adresse IP de l'utilisateur
        $userIp = $this->getRequestIp();

        // Récupération du pays de l'utilisateur
//        $userCountry = $this->utility->getUserCountry($userIp);
//        $userLocation = $this->utility->getUserLocation($userIp ?? '102.67.254.134');

        //Affecter une valeur à l'IP en developpement
        if ($userIp === '127.0.0.1') $userIp = '102.67.254.134';

        // Gestion de la session
        $session = $this->requestStack->getSession();
        if ($session->get($userIp)){
            $userLocation = $session->get($userIp);
        }else{
            $userLocation = $this->utility->getUserLocation($userIp);
            $session->set($userIp, $userLocation);
        }
//        dd($userLocation);

        $userLocationData = json_decode($userLocation, true); //dd($userLocationData);

        $logEntry = [
            'datetime' => (new \DateTime('now', new \DateTimeZone('GMT')))->format('Y-m-d H:i:s'),
//            'datetime' => date('Y-m-d H:i:s'),
            'username' => $username,
            'action' => $action,
            'ip' => $userIp,
            'country' => $userLocationData['country'],
            'city' => $userLocationData['city'],
            'region' => $userLocationData['region'],
            'location' => $userLocationData['loc'],
//            'location' => '46.813566, -71.222689',
            'org' => $userLocationData['org'],
            'timezone' => $userLocationData['timezone']
        ];

//        $logMessage = sprintf(
//            "[%s] l'utilisateur '%s' a effectué l'action : %s\n",
//            date('Y-m-d H:i:s'),
//            $username,
//            $action
//        );

//        file_put_contents($this->logFilePath, $logMessage, FILE_APPEND);

        $logEntries = [];
        if (file_exists($this->logFilePath)){
            $logEntries = unserialize(file_get_contents($this->logFilePath));
        }

        $logEntries[] = $logEntry;
        file_put_contents($this->logFilePath, serialize($logEntries));

    }

    public function getSortedLogEntries()
    {
        if (file_exists($this->logFilePath)){
            $logEntries = unserialize(file_get_contents($this->logFilePath));
            usort($logEntries, function ($a,$b){
                return strtotime($b['datetime']) - strtotime($a['datetime']);
            });

            return $logEntries;
        }

        return [];
    }

    public function getSortedLogEntriesForView()
    {
        return $this->getSortedLogEntries();
    }

    public function getUniqueIPs(): array
    {
        $ipsUnique = [];
        foreach ($this->getSortedLogEntries() as $logEntry){
            $ip = $logEntry['ip'];
            $location = $logEntry['location'];
            list($latitude, $longitude) = explode(',', $location);
            $latitude = (float) $latitude;
            $longitude = (float) $longitude;

            if (!in_array($ip, array_column($ipsUnique, 'ip'), true)){
                $ipsUnique[] = [
                    'ip' => $ip,
                    'latitude' => $latitude,
                    'longitude' => $longitude
                ];
            }

//            if (!in_array($ip, $ipsUnique, true)) {
//                $ipsUnique[] = $ip;
//            }
        }

        return $ipsUnique;
    }

    public function getUserConnected()
    {

    }

    private function getRequestIp(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request instanceof Request) {
            return $request->getClientIp();
        }

        return 'Non disponible';
    }
}