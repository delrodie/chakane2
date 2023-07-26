<?php

namespace App\EventSubscriber;

use App\Entity\Cgu;
use App\Entity\Collection;
use App\Entity\ConditionRetour;
use App\Entity\Confidentialite;
use App\Entity\Contact;
use App\Entity\Marque;
use App\Entity\MentionLegale;
use App\Entity\PolitiqueRetour;
use App\Entity\Slide;
use App\Repository\AllRepository;
use App\Service\UserActivityLogger;
use App\Service\Utility;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Validator\Constraints\All;

class DatabaseActivityLogSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserActivityLogger $userActivityLogger,
        private AllRepository $allRepository,
        private Utility $utility,
        private EntityManagerInterface $entityManager
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

        // Slide
        if ($entity instanceof Slide) {
            $this->userActivityLogger->logActivity("a {$action} le slide intitulé '{$entity->getTitre()}'");
            $this->allRepository->allCache('slides', true);
        }

        // Marque
        if ($entity instanceof Marque){
            $this->userActivityLogger->logActivity("a {$action} la marque intitulé '{$entity->getTitre()}'");
            $this->allRepository->allCache('marque', true);
        }

        // Collection
        if ($entity instanceof Collection){
            $this->userActivityLogger->logActivity("a {$action} la marque intitulé '{$entity->getTitre()}'");
            $this->allRepository->allCache('collections', true);
        }

        // Contact
        if ($entity instanceof Contact){
            $this->userActivityLogger->logActivity("a {$action} le contact");
            $this->allRepository->allCache('contact', true);
        }
    }

}