<?php

namespace AppBundle\Serializer\Subscriber;

use AppBundle\Entity\Product;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use Psr\Log\LoggerInterface;

class ProductPreDeserializationSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            [
                'class' => 'AppBundle\Entity\Product',
                'event' => 'serializer.pre_deserialize',
                'format' => 'json',
                'method' => 'attachTargetIdOnPreDeserializeJson',
            ]
        ];
    }

    public function attachTargetIdOnPreDeserializeJson(PreDeserializeEvent $event)
    {
        $target = $event
            ->getContext()
            ->attributes
            ->get('target');

        if ($target->isEmpty()) {
            return $event;
        }

        /** @var Product $product */
        $product = $target->get();

        $data = $event->getData();
        $data['id'] = $product->getId();
        $event->setData($data);

        return $event;
    }
}
