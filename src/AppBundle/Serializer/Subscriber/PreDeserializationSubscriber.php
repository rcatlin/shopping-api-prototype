<?php

namespace AppBundle\Serializer\Subscriber;

use AppBundle\Entity\IdentifiableInterface;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Context;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;

class PreDeserializationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            [
                'class' => 'AppBundle\Entity\Category',
                'event' => 'serializer.pre_deserialize',
                'format' => 'json',
                'method' => 'attachIdentifiableTargetId',
            ],
            [
                'class' => 'AppBundle\Entity\Product',
                'event' => 'serializer.pre_deserialize',
                'format' => 'json',
                'method' => 'attachIdentifiableTargetId',
            ],
            [
                'class' => 'AppBundle\Entity\Retailer',
                'event' => 'serializer.pre_deserialize',
                'format' => 'json',
                'method' => 'attachIdentifiableTargetId',
            ],
        ];
    }

    public function attachIdentifiableTargetId(PreDeserializeEvent $event)
    {
        $context = $event->getContext();

        if (
            !$this->currentPathIsAtTopLevel($event->getContext())
            || (null === ($target = $this->getNonEmptyContextTarget($context)))
            || !($target instanceof IdentifiableInterface)
        ) {
            return $event;
        }

        return $this->mutateEventDataWithId($event, $target->getId());
    }

    private function currentPathIsAtTopLevel(Context $context)
    {
        return empty($context->getCurrentPath());
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
     * @param Context $context
     *
     * @return IdentifiableInterface|null|object
     */
    private function getNonEmptyContextTarget(Context $context)
    {
        $target = $context
            ->attributes
            ->get('target');

        if ($target->isEmpty()) {
            return null;
        }

        return $target->get();
    }
}
