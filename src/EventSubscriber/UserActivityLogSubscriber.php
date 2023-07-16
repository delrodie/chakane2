<?php

namespace App\EventSubscriber;

use App\Service\UserActivityLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class UserActivityLogSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserActivityLogger $userActivityLogger
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        // Récupération de la route
        $route = $event->getRequest()->get('_route');

        switch ($route){
            case 'app_backend_dashboard_index':
                $this->userActivityLogger->logActivity("s'est connecté(e) au tableau de bord");
                break;
            case 'app_login':
                $this->userActivityLogger->logActivity("a effectué une tentative de connexion au système");
                break;
            case 'app_logout':
                $this->userActivityLogger->logActivity("a effectué une deconnexion du système");
                break;
        }
    }

}
