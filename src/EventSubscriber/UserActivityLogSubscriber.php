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
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        // Récupération de la route
        $route = $event->getRequest()->get('_route');

        if (array_key_exists($route, $this->activityMessages)) {
            $activityMessage = $this->activityMessages[$route];
            $this->userActivityLogger->logActivity($activityMessage);
        }

        // Passer la route actuelle au template
        $event->getRequest()->attributes->set('currentRoute', $route);
    }

    private array $activityMessages = [
        'app_backend_dashboard_index' => "s'est connecté(e) au tableau de bord",
        'app_login' => "a effectué une tentative de connexion au système",
        'app_backend_cgu_new' => "a visité la page d'enregistrement de la condition générale d'utilisation",
        'app_backend_cgu_edit' => "a visité la page de modification de la condition générale d'utilisation",
        'app_backend_cgu_show' => "a affiché la condition générale d'utilisation",
        'app_backend_cgu_delete' => "a effectué la suppression de la condition générale d'utilisation",
        'app_backend_slide_index' => "a affiché la liste des slides",
        'app_backend_slide_new' => "a visité la page d'enregistrement des slides",
        'app_backend_slide_edit' => "a visité la page de modification des slides",
        'app_backend_marque_new' => "a visité la page d'enregistrement des marques",
        'app_backend_marque_edit' => "a visité la page de modification des marques",
        'app_backend_collection_index' => "a visité la page de liste des collections",
        'app_backend_collection_new' => "a visité la page d'enregistrement des collections",
        'app_backend_collection_edit' => "a visité la page de modification des collections",
        'app_backend_contact_edit' => "a visité la page de modification de contact",
        'app_backend_contact_new' => "a visité la page d'enregistrement de contact",
        'app_backend_type_index' => "a visité la page d'affichage de type",
        'app_backend_type_edit' => "a visité la page de modification de type",
    ];


}
