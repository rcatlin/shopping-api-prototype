<?php

namespace AppBundle;

use Symfony\Component\HttpFoundation\Response;

trait RendersJson
{
    /**
     * @param array $content
     * @param int $status
     * @param array $headers
     *
     * @return Response
     */
    public function renderJson($status, array $content = [], array $headers = [])
    {
        $headers['Content-Type'] = 'text/json';

        return new Response(
            json_encode($content),
            $status,
            $headers
        );
    }
}
