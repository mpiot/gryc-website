<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\MultipleAlignment;
use AppBundle\Utils\TokenGenerator;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class MultipleAlignmentSubscriber implements EventSubscriber
{
    private $tokenGenerator;
    private $producer;
    private $session;

    public function __construct(TokenGenerator $tokenGenerator, ProducerInterface $producer, Session $session)
    {
        $this->tokenGenerator = $tokenGenerator;
        $this->producer = $producer;
        $this->session = $session;
    }

    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'postPersist',
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if (!$object instanceof MultipleAlignment) {
            return;
        }

        $token = $this->tokenGenerator->generateToken();
        $object->setName($token);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if (!$object instanceof MultipleAlignment) {
            return;
        }

        // Publish in Messaging Queue
        $this->producer->publish($object->getId());

        // Set at last MultipleAlignment in User session
        $this->session->set('last_multiple_alignment', $object->getId());
    }
}