<?php

namespace AppBundle\Serializer\Subscriber;

use AppBundle\Entity\IdentifiableInterface;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;

class PreDeserializationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            [
                'class' => 'AppBundle\Entity\Product',
                'event' => 'serializer.pre_deserialize',
                'format' => 'json',
                'method' => 'attachIdentifiableTargetId',
            ],
        ];
    }

    public function attachIdentifiableTargetId(PreDeserializeEvent $event)
    {
        if (null === ($target = $this->getNonEmptyContextTarget($event))) {
            return $event;
        }

        if (!($target instanceof IdentifiableInterface)) {
            return $event;
        }

        return $this->mutateEventDataWithId($event, $target->getId());
    }

    /**
     * @param PreDeserializeEvent $event
     * @param mixed $id
     *
     * @return PreDeserializeEvent
     */
    private function mutateEventDataWithId(PreDeserializeEvent $event, $id)
    {
        $data = $event->getData();
        $data['id'] = $id;
        $event->setData($data);

        return $event;
    }

    /**
     * @param PreDeserializeEvent $event
     *
     * @return IdentifiableInterface|object|null
     */
    private function getNonEmptyContextTarget(PreDeserializeEvent $event)
    {
        $target = $event
            ->getContext()
            ->attributes
            ->get('target');

        if ($target->isEmpty()) {
            return null;
        }

        return $target->get();
    }
}
