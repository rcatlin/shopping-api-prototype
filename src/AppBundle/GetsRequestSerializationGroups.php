<?php

namespace AppBundle;

use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;

trait GetsRequestSerializationGroups
{
    public function getSerializationContextFromRequest(Request $request, SerializationContext $context = null)
    {
        return $this
            ->getContext($context)
            ->setGroups(
                $this->getIncludes($request)
            )
            ->enableMaxDepthChecks();
    }

    private function getContext(SerializationContext $context = null) {
        if ($context === null) {
            return new SerializationContext();
        }

        return $context;
    }

    private function getIncludes(Request $request)
    {
        $includes = $request->query->get('includes', '');

        $includes = explode(',', $includes);

        foreach ($includes as $key => $include) {
            $includes[$key] = trim($include);
        }

        $includes[] = 'default';

        return $includes;
    }
}
