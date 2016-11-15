<?php

namespace AppBundle\Controller;

use AppBundle\Handler\RetailerHandler;
use AppBundle\RendersJson;
use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use InvalidArgumentException;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/retailers")
 */
class RetailerGetController extends FOSRestController
{
    use RendersJson;

    /**
     * @DI\Inject("handler.retailer")
     *
     * @var RetailerHandler
     */
    private $handler;

    /**
     * @DI\Inject("jms_serializer", required=true)
     *
     * @var Serializer
     */
    private $serializer;

    /**
     * Retrieves a Retailer by UUID
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Retrieve a Retailer by UUID",
     *     statusCodes={
     *         200="Retailer with given UUID found",
     *         400="Invalid UUID provided",
     *         404="Retailer was not found given the UUID"
     *     }
     * )
     *
     * @Route("/{uuid}", name="api_get_retailer")
     * @Method({"GET"})
     *
     * @param string $uuid
     *
     * @return Response
     */
    public function get($uuid)
    {
        try {
            $retailer = $this->handler->get($uuid);
        } catch (InvalidArgumentException $exception) {
            return $this->renderJson(400, [
                'errors' => [$exception->getMessage()],
            ]);
        } catch (EntityNotFoundException $exception) {
            return $this->renderJson(404, [
                'errors' => [$exception->getMessage()],
            ]);
        }

        return $this->renderJson(200, [
            'result' => $this->serializer->toArray($retailer),
        ]);
    }
}
