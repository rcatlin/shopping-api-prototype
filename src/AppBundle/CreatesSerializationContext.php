<?php

namespace AppBundle;

use JMS\Serializer\SerializationContext;

trait CreatesSerializationContext
{
    public function createSerializationContext(array $includes = [], SerializationContext $context = null)
    {
        return $this
            ->getContext($context)
            ->setGroups($includes)
            ->enableMaxDepthChecks();
    }

    private function getContext(SerializationContext $context = null) {
        if ($context === null) {
            return new SerializationContext();
        }

        return $context;
    }
}
