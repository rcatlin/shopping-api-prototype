<?php

namespace AppBundle;

use Symfony\Component\HttpFoundation\Response;

trait RenderErrors
{
    function renderErrors(array $errors, array $headers = [])
    {
        $headers['Content-Type'] = 'text/json';

        return new Response(
            json_encode($errors),
            400,
            $headers
        );
    }
}
