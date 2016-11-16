<?php

namespace AppBundle\Controller;

use AppBundle\RendersJson;
use FOS\RestBundle\Controller\Annotations\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class StatusController
{
    use RendersJson;

    /**
     * @Route("/api/status")
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getStatus()
    {
        return $this->renderJson(200, [
            'result' => [
                'status' => 'OK',
            ],
        ]);
    }
}
