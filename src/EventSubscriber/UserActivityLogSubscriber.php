<?php

namespace App\EventSubscriber;

use App\Entity\Cgu;
use App\Entity\ConditionRetour;
use App\Entity\Confidentialite;
use App\Entity\MentionLegale;
use App\Entity\PolitiqueRetour;
use App\Service\UserActivityLogger;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
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
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove
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
            case 'app_backend_cgu_new':
                $this->userActivityLogger->logActivity("a visité la page d'enregistrement de la condition générale d'utilisation");
                break;
            case 'app_backend_cgu_edit':
                $this->userActivityLogger->logActivity("a visité la page de modification de la condition générale d'utilisation");
                break;
            case 'app_backend_cgu_show':
                $this->userActivityLogger->logActivity("a affiché la condition générale d'utilisation");
                break;
            case 'app_backend_cgu_delete':
                $this->userActivityLogger->logActivity("a effectué la suppression de la condition générale d'utilisation");
                break;
            case 'app_backend_slide_index':
                $this->userActivityLogger->logActivity("a affiché la liste des slides");
                break;
            case 'app_backend_slide_new':
                $this->userActivityLogger->logActivity("a visité la page d'enregistrement des slides ");
                break;
            case 'app_backend_slide_edit':
                $this->userActivityLogger->logActivity("a visité la page de modification des slides");
        }
    }


}
