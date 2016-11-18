<?php

namespace AppBundle\Serializer;

use Exception\Serializer\Construction\ObjectNotConstructedException;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Construction\DoctrineObjectConstructor as BaseDoctrineObjectConstructor;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\VisitorInterface;
use JMS\Serializer\Metadata\ClassMetadata;

class DoctrineObjectConstructor extends BaseDoctrineObjectConstructor
{
    /**
     * @param VisitorInterface $visitor
     * @param ClassMetadata $metadata
     * @param mixed $data
     * @param array $type
     * @param DeserializationContext $context
     *
     * @return object
     *
     * @throws ObjectNotConstructedException
     */
    public function construct(
        VisitorInterface $visitor,
        ClassMetadata $metadata,
        $data,
        array $type,
        DeserializationContext $context
    ) {
        $object = parent::construct($visitor, $metadata, $data, $type, $context);

        if ($object === null) {
            throw new ObjectNotConstructedException(
                $metadata->name,
                $data,
                $context->getCurrentPath()
            );
        }

        return $object;
    }
}
