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
        private RequestStack $requestStack
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

        $logEntry = [
            'datetime' => (new \DateTime('now', new \DateTimeZone('GMT')))->format('Y-m-d H:i:s'),
//            'datetime' => date('Y-m-d H:i:s'),
            'username' => $username,
            'action' => $action,
            'ip' => $this->getRequestIp()
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

    private function getRequestIp(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request instanceof Request) {
            return $request->getClientIp();
        }

        return 'Non disponible';
    }
}