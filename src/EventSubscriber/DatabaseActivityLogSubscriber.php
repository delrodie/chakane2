<?php

namespace App\EventSubscriber;

use App\Entity\Cgu;
use App\Entity\ConditionRetour;
use App\Entity\Confidentialite;
use App\Entity\MentionLegale;
use App\Entity\PolitiqueRetour;
use App\Service\UserActivityLogger;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class DatabaseActivityLogSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserActivityLogger $userActivityLogger
    )
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->extracted($args, 'enregistré');
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->extracted($args, 'modifié');
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->extracted($args, 'supprimé');
    }

    /**
     * @param LifecycleEventArgs $args
     * @param string $action
     * @return void
     */
    public function extracted(LifecycleEventArgs $args, string $action): void
    {
        $entity = $args->getObject();

        //Enregistrement de CGU
        if ($entity instanceof Cgu) {
            $this->userActivityLogger->logActivity("a {$action} la CGU intitulée '{$entity->getTitre()}' ");
        }

        // Enregistrement de Condition de retour
        if ($entity instanceof ConditionRetour) {
            $this->userActivityLogger->logActivity("a {$action} la condition de retour intitulée '{$entity->getTitre()}'");
        }

        // Enregistrement de confidentialité
        if ($entity instanceof Confidentialite) {
            $this->userActivityLogger->logActivity("a {$action} la confidentialité intitulée '{$entity->getTitre()}'");
        }

        // Mention legale
        if ($entity instanceof MentionLegale) {
            $this->userActivityLogger->logActivity("a {$action} la mention légale intitulée '{$entity->getTitre()}'");
        }

        // Politique de retour
        if ($entity instanceof PolitiqueRetour) {
            $this->userActivityLogger->logActivity("a {$action} la politique de retour intitulée '{$entity->getTitre()}'");
        }
    }

}