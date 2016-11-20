<?php

namespace AppBundle;

use Symfony\Component\HttpFoundation\Request;

trait GetsIncludes
{
    public function getIncludes(Request $request)
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
